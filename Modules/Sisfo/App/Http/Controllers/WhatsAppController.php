<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller;
use Symfony\Component\Process\Process;
use Modules\Sisfo\App\Models\Log\QRCodeWAModel;
use Modules\Sisfo\App\Models\UserModel;

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

        return view("sisfo::WhatsAppManagement.index", [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
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
     * Save QR code scan log when user scans QR code
     */
    public function saveQRCodeLog(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nomor_pengirim' => 'required|string|max:20'
            ]);

            $user = Auth::user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi'
                ]);
            }

            $nomorPengirim = $request->nomor_pengirim;
            $userScan = $user->nama_pengguna ?? 'Unknown User';

            // Get user's hak akses - perbaiki logika ini
            $haScan = 'Unknown';
            try {
                // Method 1: Cek melalui relasi userHakAkses
                if ($user->userHakAkses && $user->userHakAkses->count() > 0) {
                    $hakAksesUser = $user->userHakAkses->first();
                    if ($hakAksesUser && $hakAksesUser->hakAkses) {
                        $haScan = $hakAksesUser->hakAkses->nama_hak_akses;
                    }
                }

                // Method 2: Fallback - ambil dari field langsung jika ada
                if ($haScan === 'Unknown' && isset($user->hak_akses)) {
                    $haScan = $user->hak_akses;
                }

                // Method 3: Fallback - cek role atau level user
                if ($haScan === 'Unknown') {
                    if (isset($user->role)) {
                        $haScan = $user->role;
                    } elseif (isset($user->level)) {
                        $haScan = $user->level;
                    } elseif (isset($user->user_type)) {
                        $haScan = $user->user_type;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error getting user hak akses: ' . $e->getMessage());
                $haScan = 'User';
            }

            // Log untuk debugging
            Log::info('Saving QR Code Log', [
                'nomor_pengirim' => $nomorPengirim,
                'user_scan' => $userScan,
                'ha_scan' => $haScan,
                'user_id' => $user->id ?? 'unknown'
            ]);

            // Create new scan log
            $scanLog = QRCodeWAModel::createScanLog($nomorPengirim, $userScan, $haScan);

            if ($scanLog) {
                Log::info('QR Code log saved successfully', ['scan_log_id' => $scanLog->log_qrcode_wa_id]);

                return response()->json([
                    'success' => true,
                    'message' => 'Log QR code scan berhasil disimpan',
                    'scan_data' => $scanLog
                ]);
            } else {
                Log::error('Failed to save QR code log - createScanLog returned null');

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan log QR code scan'
                ]);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors()['nomor_pengirim'] ?? ['Data tidak valid'])
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving QR code log: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get QR code scan status
     */
    public function getQRCodeStatus(): JsonResponse
    {
        try {
            $isRunning = $this->isServerRunning();
            $statusData = $this->getServerStatusData();
            $isAuthenticated = $statusData['authenticated'] ?? false;
            $latestScan = QRCodeWAModel::getLatestActiveScan();

            // Debug log
            Log::debug('QR Code Status Check', [
                'is_running' => $isRunning,
                'is_authenticated' => $isAuthenticated,
                'has_latest_scan' => $latestScan ? true : false,
                'latest_scan_id' => $latestScan ? $latestScan->log_qrcode_wa_id : null
            ]);

            return response()->json([
                'success' => true,
                'server_running' => $isRunning,
                'authenticated' => $isAuthenticated,
                'latest_scan' => $latestScan,
                'status' => $this->getQRCodeStatusText($isRunning, $isAuthenticated, $latestScan)
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting QR code status: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Send WhatsApp message
     */
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'number' => 'required|string',
                'message' => 'required|string'
            ]);

            if (!$this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server tidak berjalan'
                ]);
            }

            $postData = [
                'number' => $request->number,
                'message' => $request->message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/send-message');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen(json_encode($postData))
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);

                if ($data && isset($data['success']) && $data['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Pesan berhasil dikirim',
                        'data' => $data
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $data['message'] ?? 'Gagal mengirim pesan'
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mengirim pesan ke server WhatsApp'
            ]);
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get WhatsApp QR Code as base64 image
     */
    public function getQRCode(): JsonResponse
    {
        try {
            if (!$this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server tidak berjalan'
                ]);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/qr-image');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'QR Code tidak tersedia'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Force restart WhatsApp server
     */
    public function forceRestart(): JsonResponse
    {
        try {
            // Stop server
            $this->stopServer();
            sleep(3);

            // Start server
            $startResult = $this->startServer();

            return response()->json([
                'success' => true,
                'message' => 'WhatsApp server berhasil direstart',
                'start_result' => $startResult->getData()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get server health status
     */
    public function getHealth(): JsonResponse
    {
        try {
            $isRunning = $this->isServerRunning();
            $statusData = $this->getServerStatusData();

            return response()->json([
                'success' => true,
                'server_running' => $isRunning,
                'server_status' => $statusData,
                'timestamp' => date('Y-m-d H:i:s'),
                'uptime' => $this->getServerUptime()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get server logs
     */
    public function getLogs(Request $request): JsonResponse
    {
        try {
            $lines = $request->get('lines', 50);
            $logFile = $this->whatsappPath . DIRECTORY_SEPARATOR . 'server.log';

            if (!file_exists($logFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File log tidak ditemukan'
                ]);
            }

            $logs = [];
            if (PHP_OS_FAMILY === 'Windows') {
                exec("powershell Get-Content '$logFile' -Tail $lines", $logs);
            } else {
                exec("tail -n $lines '$logFile'", $logs);
            }

            return response()->json([
                'success' => true,
                'logs' => $logs,
                'total_lines' => count($logs)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Clear server logs
     */
    public function clearLogs(): JsonResponse
    {
        try {
            $logFiles = [
                $this->whatsappPath . DIRECTORY_SEPARATOR . 'server.log',
                $this->whatsappPath . DIRECTORY_SEPARATOR . 'error.log',
                $this->whatsappPath . DIRECTORY_SEPARATOR . 'debug.log'
            ];

            $clearedFiles = 0;
            foreach ($logFiles as $logFile) {
                if (file_exists($logFile)) {
                    file_put_contents($logFile, '');
                    $clearedFiles++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Berhasil membersihkan $clearedFiles file log"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get QR code status text
     */
    private function getQRCodeStatusText($isRunning, $isAuthenticated, $latestScan): string
    {
        if (!$isRunning) {
            return 'server-belum-distart';
        }

        // Jika server berjalan dan authenticated, tapi belum ada log scan
        if ($isAuthenticated && !$latestScan) {
            return 'authenticated-belum-log'; // Status baru untuk case ini
        }

        // Server running, authenticated, dan ada scan log
        if ($isAuthenticated && $latestScan) {
            return 'sudah-terscan';
        }

        // Server running tapi belum authenticated
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
     * Get server uptime
     */
    private function getServerUptime(): ?string
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                return $data['uptime'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
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

    /**
     * Validate phone number format
     */
    private function validatePhoneNumber($number): string
    {
        // Remove all non-numeric characters
        $number = preg_replace('/[^0-9]/', '', $number);

        // If starts with 0, replace with 62
        if (substr($number, 0, 1) === '0') {
            $number = '62' . substr($number, 1);
        }

        // If doesn't start with 62, add it
        if (substr($number, 0, 2) !== '62') {
            $number = '62' . $number;
        }

        return $number;
    }

    /**
     * Format message for WhatsApp
     */
    private function formatMessage($message): string
    {
        // Replace line breaks with proper WhatsApp line breaks
        $message = str_replace(['\r\n', '\r', '\n'], "\n", $message);

        // Trim whitespace
        $message = trim($message);

        return $message;
    }

    /**
     * Get process ID of WhatsApp server
     */
    private function getServerPID(): ?int
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                exec('netstat -aon | find ":3000" | find "LISTENING"', $output);
                if (!empty($output)) {
                    $line = trim($output[0]);
                    $parts = preg_split('/\s+/', $line);
                    return isset($parts[4]) ? (int)$parts[4] : null;
                }
            } else {
                exec('lsof -ti :3000', $output);
                return !empty($output) ? (int)$output[0] : null;
            }

            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Kill server process by PID
     */
    private function killServerProcess(): bool
    {
        try {
            $pid = $this->getServerPID();

            if (!$pid) {
                return false;
            }

            if (PHP_OS_FAMILY === 'Windows') {
                exec("taskkill /F /PID $pid", $output, $returnCode);
            } else {
                exec("kill -9 $pid", $output, $returnCode);
            }

            return $returnCode === 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function autoSaveQRCodeLog(Request $request): JsonResponse
    {
        try {
            Log::info('Received auto save request', $request->all());

            $request->validate([
                'nomor_pengirim' => 'required|string|max:20',
                'scan_source' => 'string|nullable',
                'timestamp' => 'string|nullable'
            ]);

            $nomorPengirim = $request->nomor_pengirim;

            // Jika nomor adalah 'auto_detected', coba ambil dari server WhatsApp
            if ($nomorPengirim === 'auto_detected') {
                $connectedPhone = $this->getConnectedPhoneFromServer();
                if ($connectedPhone) {
                    $nomorPengirim = $connectedPhone;
                    Log::info('Phone number auto detected: ' . $nomorPengirim);
                } else {
                    Log::warning('Auto detected phone number not available');
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor WhatsApp tidak dapat dideteksi otomatis'
                    ]);
                }
            }

            // Get current authenticated user atau buat default
            $user = Auth::user();
            if (!$user) {
                $user = $this->getSystemUser();
                if (!$user) {
                    $user = $this->createDefaultUser();
                }
            }

            $userScan = $user->nama_pengguna ?? 'Auto System';
            $haScan = $this->getUserHakAkses($user);

            Log::info('Auto Saving QR Code Log', [
                'nomor_pengirim' => $nomorPengirim,
                'user_scan' => $userScan,
                'ha_scan' => $haScan,
                'user_id' => $user->id ?? 'auto',
                'scan_source' => $request->scan_source ?? 'auto_detected'
            ]);

            // Create new scan log
            $scanLog = QRCodeWAModel::createScanLog($nomorPengirim, $userScan, $haScan);

            if ($scanLog) {
                Log::info('Auto QR Code log saved successfully', [
                    'scan_log_id' => $scanLog->log_qrcode_wa_id,
                    'nomor_pengirim' => $nomorPengirim
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Log QR code scan otomatis berhasil disimpan',
                    'scan_data' => $scanLog,
                    'auto_detected' => true
                ]);
            } else {
                Log::error('Failed to auto save QR code log - createScanLog returned null');

                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan log QR code scan otomatis'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error auto saving QR code log: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get connected phone from WhatsApp server
     */
    private function getConnectedPhoneFromServer(): ?string
    {
        try {
            if (!$this->isServerRunning()) {
                return null;
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/connected-phone');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                return $data['connected_phone'] ?? null;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting connected phone: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create default user for auto save
     */
    private function createDefaultUser()
    {
        try {
            // Return virtual user object untuk auto save
            return (object) [
                'id' => 0,
                'nama_pengguna' => 'Auto System',
                'userHakAkses' => collect([]),
                'hak_akses' => 'System',
                'role' => 'Auto',
                'level' => 'System'
            ];
        } catch (\Exception $e) {
            Log::error('Error creating default user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get system user for auto save when no user is logged in
     */
    private function getSystemUser()
    {
        try {
            // Try to get current session user first
            if (Auth::check()) {
                return Auth::user();
            }

            // Try to get latest active admin user
            $systemUser = UserModel::whereHas('userHakAkses.hakAkses', function ($query) {
                $query->where('nama_hak_akses', 'like', '%admin%')
                    ->orWhere('nama_hak_akses', 'like', '%administrator%');
            })
                ->where('isDeleted', 0)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($systemUser) {
                Log::info('Using system admin user for auto save: ' . $systemUser->nama_pengguna);
                return $systemUser;
            }

            // Fallback: get any active user
            $fallbackUser = UserModel::where('isDeleted', 0)
                ->orderBy('updated_at', 'desc')
                ->first();

            if ($fallbackUser) {
                Log::info('Using fallback user for auto save: ' . $fallbackUser->nama_pengguna);
                return $fallbackUser;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting system user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get user hak akses
     */
    private function getUserHakAkses($user): string
    {
        $haScan = 'Unknown';
        try {
            // Method 1: Cek melalui relasi userHakAkses
            if ($user->userHakAkses && $user->userHakAkses->count() > 0) {
                $hakAksesUser = $user->userHakAkses->first();
                if ($hakAksesUser && $hakAksesUser->hakAkses) {
                    // Extract string name from hakAkses object
                    $hakAksesObj = $hakAksesUser->hakAkses;
                    if (isset($hakAksesObj->nama_hak_akses)) {
                        $haScan = $hakAksesObj->nama_hak_akses;
                    } elseif (isset($hakAksesObj->hak_akses_nama)) {
                        $haScan = $hakAksesObj->hak_akses_nama;
                    } elseif (method_exists($hakAksesObj, 'toArray')) {
                        $array = $hakAksesObj->toArray();
                        if (isset($array['nama_hak_akses'])) {
                            $haScan = $array['nama_hak_akses'];
                        } elseif (isset($array['hak_akses_nama'])) {
                            $haScan = $array['hak_akses_nama'];
                        }
                    }
                }
            }

            // Method 2: Fallback - ambil dari field langsung jika ada
            if ($haScan === 'Unknown' && isset($user->hak_akses)) {
                $haScan = $user->hak_akses;
            }

            // Method 3: Fallback - cek role atau level user
            if ($haScan === 'Unknown') {
                if (isset($user->role)) {
                    $haScan = $user->role;
                } elseif (isset($user->level)) {
                    $haScan = $user->level;
                } elseif (isset($user->user_type)) {
                    $haScan = $user->user_type;
                } else {
                    $haScan = 'User'; // Default fallback
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error getting user hak akses: ' . $e->getMessage());
            $haScan = 'User';
        }

        Log::debug('Final hak akses result: ' . $haScan);
        return $haScan;
    }

    /**
     * Check connected phone number from WhatsApp server
     */
    public function getConnectedPhone(): JsonResponse
    {
        try {
            if (!$this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server tidak berjalan'
                ]);
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://localhost:3000/api/connected-phone');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 && $response) {
                $data = json_decode($response, true);
                return response()->json($data);
            }

            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat mendapatkan info nomor terhubung'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

    public function triggerScanLog(): JsonResponse
    {
        try {
            if (!$this->isServerRunning()) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp server tidak berjalan'
                ]);
            }

            $statusData = $this->getServerStatusData();
            if (!($statusData['authenticated'] ?? false)) {
                return response()->json([
                    'success' => false,
                    'message' => 'WhatsApp belum ter-authenticate'
                ]);
            }

            // Get connected phone number
            $connectedPhone = $this->getConnectedPhoneFromServer();

            if (!$connectedPhone) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor telepon tidak dapat dideteksi'
                ]);
            }

            // Create auto save request - DIPERBAIKI
            $autoSaveRequest = new Request([
                'nomor_pengirim' => $connectedPhone,
                'scan_source' => 'manual_trigger',
                'timestamp' => date('Y-m-d H:i:s') // Format database MySQL
            ]);

            return $this->autoSaveQRCodeLog($autoSaveRequest);
        } catch (\Exception $e) {
            Log::error('Error triggering scan log: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
