<?php
// filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\App\Http\Controllers\WhatsAppController.php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Symfony\Component\Process\Process;
use Modules\Sisfo\App\Models\Log\BarcodeWAModel;

class WhatsAppController extends Controller
{
    use TraitsController;

    public $breadcrumb = 'Pengaturan WhatsApp Management';
    public $pagename = 'WhatsAppManagement';
    private $whatsappPath;

    public function __construct()
    {
        $this->whatsappPath = base_path('whatsapp-api');
    }

    public function index(Request $request)
    {
        $breadcrumb = (object) [
            'title' => 'WhatsApp Management',
            'list' => ['Home', 'WhatsApp Management']
        ];

        $page = (object) [
            'title' => 'WhatsApp Server Management'
        ];

        $activeMenu = 'WhatsAppManagement';

        // Get latest barcode scan data
        $latestScan = BarcodeWAModel::getLatestActiveScan();

        return view("sisfo::WhatsAppManagement.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu,
            'latestScan' => $latestScan
        ]);
    }

    /**
     * Start WhatsApp Server
     */
    public function startServer(): JsonResponse
    {
        try {
            // Check if server is already running
            if ($this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server sudah berjalan'
                ]);
            }

            // Start server menggunakan start-whatsapp.bat
            $command = base_path('start-whatsapp.bat');
            
            if (PHP_OS_FAMILY === 'Windows') {
                // Windows - jalankan di background
                $process = new Process(['cmd', '/c', 'start', '/b', $command]);
            } else {
                // Linux/Mac
                $process = new Process(['bash', $command]);
            }

            $process->start();

            // Wait a bit to check if process started successfully
            sleep(3);

            if ($this->isServerRunning()) {
                return response()->json([
                    'success' => true,
                    'message' => 'WhatsApp server berhasil dijalankan',
                    'server_url' => 'http://localhost:3000',
                    'qr_url' => 'http://localhost:3000/qr'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan WhatsApp server'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error starting WhatsApp server: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Stop WhatsApp Server
     */
    public function stopServer(): JsonResponse
    {
        try {
            $command = base_path('stop-whatsapp.bat');
            
            if (PHP_OS_FAMILY === 'Windows') {
                exec($command, $output, $returnCode);
            } else {
                exec("bash $command", $output, $returnCode);
            }

            return response()->json([
                'success' => $returnCode === 0,
                'message' => $returnCode === 0 ? 'WhatsApp server berhasil dihentikan' : 'Gagal menghentikan server',
                'output' => implode("\n", $output)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset WhatsApp Session
     */
    public function resetSession(): JsonResponse
    {
        try {
            // Mark all active scans as deleted before reset
            BarcodeWAModel::markAllAsDeleted();

            // Stop server first
            $this->stopServer();
            sleep(2);

            // Remove session files
            $sessionPaths = [
                $this->whatsappPath . DIRECTORY_SEPARATOR . '.wwebjs_auth',
                $this->whatsappPath . DIRECTORY_SEPARATOR . '.wwebjs_cache',
                $this->whatsappPath . DIRECTORY_SEPARATOR . 'session.json',
                $this->whatsappPath . DIRECTORY_SEPARATOR . 'chrome_debug.log'
            ];

            foreach ($sessionPaths as $path) {
                if (is_dir($path)) {
                    $this->deleteDirectory($path);
                } elseif (file_exists($path)) {
                    unlink($path);
                }
            }

            // Remove all .log files
            $logFiles = glob($this->whatsappPath . DIRECTORY_SEPARATOR . '*.log');
            foreach ($logFiles as $logFile) {
                if (file_exists($logFile)) {
                    unlink($logFile);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Session WhatsApp berhasil direset. Silakan start server untuk scan QR code baru.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get Server Status
     */
    public function getStatus(): JsonResponse
    {
        $isRunning = $this->isServerRunning();
        $isAuthenticated = $this->isWhatsAppAuthenticated();
        $latestScan = BarcodeWAModel::getLatestActiveScan();
        
        return response()->json([
            'running' => $isRunning,
            'authenticated' => $isAuthenticated,
            'message' => $isRunning ? 'Server berjalan' : 'Server tidak berjalan',
            'server_url' => $isRunning ? 'http://localhost:3000' : null,
            'qr_url' => $isRunning ? 'http://localhost:3000/qr' : null,
            'latest_scan' => $latestScan
        ]);
    }

    /**
     * Get QR Code for WhatsApp Authentication
     */
    public function getQRCode(): JsonResponse
    {
        try {
            if (!$this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server belum berjalan'
                ]);
            }

            // Check if already authenticated
            if ($this->isWhatsAppAuthenticated()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp sudah ter-authenticate',
                    'authenticated' => true
                ]);
            }

            return response()->json([
                'success' => true,
                'qr_url' => 'http://localhost:3000/qr',
                'message' => 'QR Code tersedia untuk scan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save barcode scan log when user scans QR code
     */
    public function saveBarcodeLog(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nomor_pengirim' => 'required|string|max:20'
            ]);

            $user = Auth::user();
            $nomorPengirim = $request->nomor_pengirim;
            $userScan = $user->nama_pengguna;
            $haScan = $user->userHakAkses->first()->hakAkses->nama_hak_akses ?? 'Unknown';

            // Create new scan log
            $scanLog = BarcodeWAModel::createScanLog($nomorPengirim, $userScan, $haScan);

            return response()->json([
                'success' => true,
                'message' => 'Log barcode scan berhasil disimpan',
                'scan_data' => $scanLog
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving barcode log: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get barcode scan status
     */
    public function getBarcodeStatus(): JsonResponse
    {
        try {
            $isRunning = $this->isServerRunning();
            $isAuthenticated = $this->isWhatsAppAuthenticated();
            $latestScan = BarcodeWAModel::getLatestActiveScan();

            return response()->json([
                'success' => true,
                'server_running' => $isRunning,
                'authenticated' => $isAuthenticated,
                'latest_scan' => $latestScan,
                'status' => $this->getBarcodeStatusText($isRunning, $isAuthenticated, $latestScan)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get barcode status text
     */
    private function getBarcodeStatusText($isRunning, $isAuthenticated, $latestScan): string
    {
        if (!$isRunning) {
            return 'server-belum-distart';
        }

        // Server running, check authentication
        if ($isAuthenticated && $latestScan) {
            return 'sudah-terscan';
        }

        // Server running but not authenticated or no scan log
        return 'belum-terscan';
    }

    /**
     * Check if WhatsApp server is running
     */
    private function isServerRunning(): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            exec('netstat -aon | find ":3000" | find "LISTENING"', $output);
            return !empty($output);
        } else {
            exec('lsof -i :3000', $output);
            return !empty($output);
        }
    }

    /**
     * Check if WhatsApp is authenticated (via API call)
     */
    private function isWhatsAppAuthenticated(): bool
    {
        if (!$this->isServerRunning()) {
            return false;
        }

        try {
            // Try to call WhatsApp API status endpoint
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/status');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                
                // Check if WhatsApp is authenticated
                if (isset($data['authenticated']) && $data['authenticated'] === true) {
                    return true;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Error checking WhatsApp authentication: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if has active WhatsApp session files (fallback method)
     */
    private function hasActiveSession(): bool
    {
        $sessionPath = $this->whatsappPath . DIRECTORY_SEPARATOR . '.wwebjs_auth';
        return is_dir($sessionPath) && !empty(glob($sessionPath . DIRECTORY_SEPARATOR . '*'));
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir): bool
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        return rmdir($dir);
    }
}