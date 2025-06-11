<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Symfony\Component\Process\Process;
use Modules\Sisfo\App\Models\Log\QRCodeWAModel;

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
            'title' => 'Manajemen Server WhatsApp'
        ];

        $activeMenu = 'WhatsAppManagement';

        // Get latest barcode scan data
        $latestScan = QRCodeWAModel::getLatestActiveScan();

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
                    'message' => 'WhatsApp server berhasil dijalankan. Tunggu beberapa saat untuk QR Code muncul.',
                    'server_url' => 'http://localhost:3000',
                    'qr_url' => 'http://localhost:3000/qr'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menjalankan WhatsApp server. Pastikan Node.js sudah terinstall dan port 3000 tersedia.'
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
            QRCodeWAModel::markAllAsDeleted();

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
        $statusData = $this->getServerStatusData();
        $latestScan = QRCodeWAModel::getLatestActiveScan();
        
        return response()->json([
            'running' => $isRunning,
            'authenticated' => $statusData['authenticated'] ?? false,
            'message' => $isRunning ? 'Server berjalan' : 'Server tidak berjalan',
            'server_url' => $isRunning ? 'http://localhost:3000' : null,
            'qr_url' => $isRunning ? 'http://localhost:3000/qr' : null,
            'qr_available' => $statusData['qr_available'] ?? false,
            'latest_scan' => $latestScan,
            'server_status' => $statusData['status'] ?? 'tidak_tersedia'
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

            $statusData = $this->getServerStatusData();

            // Check if already authenticated
            if ($statusData['authenticated'] ?? false) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp sudah ter-authenticate',
                    'authenticated' => true
                ]);
            }

            // Try to get QR image from API
            $qrImageData = $this->getQRImageFromAPI();
            
            return response()->json([
                'success' => true,
                'qr_url' => 'http://localhost:3000/qr',
                'qr_image' => $qrImageData['qr_image'] ?? null,
                'qr_text' => $qrImageData['qr_text'] ?? null,
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
     * Get QR Image from Node.js API
     */
    private function getQRImageFromAPI(): array
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/qr-image');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                if ($data && $data['success']) {
                    return $data;
                }
            }

            return [];

        } catch (\Exception $e) {
            Log::error('Error getting QR image from API: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Save barcode scan log when user scans QR code
     */
    public function saveQRCodeLog(Request $request): JsonResponse
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
            $scanLog = QRCodeWAModel::createScanLog($nomorPengirim, $userScan, $haScan);

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
    public function getQRCodeStatus(): JsonResponse
    {
        try {
            $isRunning = $this->isServerRunning();
            $statusData = $this->getServerStatusData();
            $isAuthenticated = $statusData['authenticated'] ?? false;
            $latestScan = QRCodeWAModel::getLatestActiveScan();

            return response()->json([
                'success' => true,
                'server_running' => $isRunning,
                'authenticated' => $isAuthenticated,
                'latest_scan' => $latestScan,
                'status' => $this->getQRCodeStatusText($isRunning, $isAuthenticated, $latestScan)
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
    private function getQRCodeStatusText($isRunning, $isAuthenticated, $latestScan): string
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
     * Get server status data from API
     */
    private function getServerStatusData(): array
    {
        if (!$this->isServerRunning()) {
            return ['status' => 'tidak_berjalan'];
        }

        try {
            // Call API status endpoint tanpa authorization
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
                return $data ?? ['status' => 'error_response'];
            }

            return ['status' => 'no_response'];

        } catch (\Exception $e) {
            Log::error('Error getting server status: ' . $e->getMessage());
            return ['status' => 'error'];
        }
    }

    /**
     * Check if WhatsApp is authenticated (via API call)
     */
    private function isWhatsAppAuthenticated(): bool
    {
        $statusData = $this->getServerStatusData();
        return $statusData['authenticated'] ?? false;
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