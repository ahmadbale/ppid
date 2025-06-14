@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
  $whatsappUrl = WebMenuModel::getDynamicMenuUrl('whatsapp-management');
@endphp
@extends('sisfo::layouts.template')

@section('content')
  <div class="card card-outline card-primary">
    <div class="card-header">
    <div class="row align-items-center">
      <div class="col-md-8">
      <h3 class="card-title">{{ $page->title }}</h3>
      </div>
      <div class="col-md-4 text-right">
      <div class="card-tools">
        <button id="refreshStatus" class="btn btn-sm btn-info">
        <i class="fas fa-sync"></i> Refresh Status
        </button>
      </div>
      </div>
    </div>
    </div>

    <div class="card-body">
    <!-- Informasi Server -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card border-info">
        <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-info-circle mr-2"></i>Informasi Server WhatsApp
        </h5>
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-md-3">
          <div class="info-box" id="serverStatus">
            <span class="info-box-icon bg-secondary">
            <i class="fas fa-server"></i>
            </span>
            <div class="info-box-content">
            <span class="info-box-text">Status Server</span>
            <span class="info-box-number" id="serverStatusText">Memeriksa...</span>
            </div>
          </div>
          </div>
          <div class="col-md-3">
          <div class="info-box" id="authStatus">
            <span class="info-box-icon bg-secondary">
            <i class="fas fa-mobile-alt"></i>
            </span>
            <div class="info-box-content">
            <span class="info-box-text">Status WhatsApp</span>
            <span class="info-box-number" id="authStatusText">Memeriksa...</span>
            </div>
          </div>
          </div>
          <div class="col-md-6">
          <div class="info-links">
            <h6 class="mb-3"><i class="fas fa-link mr-2"></i>Link Server</h6>
            <div class="mb-2">
            <strong>Server URL:</strong>
            <a href="http://localhost:3000" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
              <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000
            </a>
            </div>
            <div class="mb-2">
            <strong>API Status:</strong>
            <a href="http://localhost:3000/api/status" target="_blank"
              class="btn btn-sm btn-outline-primary ml-2">
              <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000/api/status
            </a>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Control Panel Server -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card border-primary">
        <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-cogs mr-2"></i>Kontrol Server
        </h5>
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-md-4">
          <button class="btn btn-success btn-block btn-lg" id="startServer">
            <i class="fas fa-play mr-2"></i>Mulai Server
          </button>
          <small class="text-muted">Jalankan server WhatsApp</small>
          </div>
          <div class="col-md-4">
          <button class="btn btn-danger btn-block btn-lg" id="stopServer">
            <i class="fas fa-stop mr-2"></i>Hentikan Server
          </button>
          <small class="text-muted">Matikan server WhatsApp</small>
          </div>
          <div class="col-md-4">
          <button class="btn btn-warning btn-block btn-lg" id="resetSession">
            <i class="fas fa-refresh mr-2"></i>Reset Sesi
          </button>
          <small class="text-muted">Reset koneksi WhatsApp</small>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Status QRCode WhatsApp - Layout Baru -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card border-success" id="qrcodeStatusCard">
        <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-qrcode mr-2"></i>Status QR Code WhatsApp
        </h5>
        </div>
        <div class="card-body">
        <div class="row">
          <!-- Container QR Scan - Sisi Kiri (Lebih Besar) -->
          <div class="col-md-7">
          <div class="qr-scan-container">
            <div id="qrcodeStatusContent" class="qr-display-area">
            <!-- Content akan dimuat secara dinamis -->
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Memuat...</span>
              </div>
              <p class="mt-2">Memuat status QR Code...</p>
            </div>
            </div>
          </div>
          </div>

          <!-- Data Log Scan - Sisi Kanan -->
          <div class="col-md-5">
          <div class="scan-info-container">
            <h6 class="mb-3 text-primary">
            <i class="fas fa-clipboard-list mr-2"></i>Informasi Terakhir Scan
            </h6>
            <div class="scan-info-card">
            <table class="table table-sm table-borderless scan-info-table" id="scanInfoTable">
              <tbody>
              <tr>
                <td class="info-label"><strong>Nomor WA Pengirim:</strong></td>
                <td class="info-value" id="info-nomor">-</td>
              </tr>
              <tr>
                <td class="info-label"><strong>User yang Scan:</strong></td>
                <td class="info-value" id="info-user">-</td>
              </tr>
              <tr>
                <td class="info-label"><strong>Hak Akses User:</strong></td>
                <td class="info-value" id="info-hakakses">-</td>
              </tr>
              <tr>
                <td class="info-label"><strong>Tanggal Scan:</strong></td>
                <td class="info-value" id="info-tanggal">-</td>
              </tr>
              </tbody>
            </table>
            </div>

            <!-- Status Indikator -->
            <div class="status-indicator mt-3">
            <div class="info-message alert-info" id="statusAlert">

              <span id="statusMessage">Memuat status...</span>
            </div>
            </div>

            <!-- Auto Save Info -->
            <div class="auto-save-info">
            <div class="info-message alert-light border-info">
              <small>
              <i class="fas fa-magic mr-2 text-info"></i>
              <strong>Auto Save:</strong> Log scan akan otomatis tersimpan setelah QR Code berhasil discan.
              </small>
            </div>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Log Aktivitas -->
    <div class="row">
      <div class="col-md-12">
      <div class="card border-secondary">
        <div class="card-header bg-secondary text-white">
        <div class="row align-items-center">
          <div class="col-md-8">
          <h5 class="card-title mb-0">
            <i class="fas fa-terminal mr-2"></i>Log Aktivitas
          </h5>
          </div>
          <div class="col-md-4 text-right">
          <button id="clearLog" class="btn btn-sm btn-light">
            <i class="fas fa-trash mr-1"></i>Bersihkan Log
          </button>
          </div>
        </div>
        </div>
        <div class="card-body">
        <div id="logContainer" class="log-container">
          <div class="log-entry text-muted">
          <span class="timestamp">[{{ date('H:i:s') }}]</span>
          <i class="fas fa-info-circle mr-1"></i>
          Sistem siap. Klik tombol untuk memulai operasi...
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    /* Info Box Styling */
    .info-box {
    transition: all 0.3s ease;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* QR Scan Container - Sisi Kiri */
    .qr-scan-container {
    min-height: 450px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e9ecef;
    padding: 20px;
    }

    .qr-display-area {
    min-height: 400px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: white;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    }

    /* QR Code Status Content */
    .qrcode-status-content {
    width: 100%;
    text-align: center;
    padding: 20px;
    }

    .qrcode-qr-placeholder {
    width: 280px;
    height: 280px;
    border: 2px dashed #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    border-radius: 12px;
    background: #f8f9fa;
    font-size: 16px;
    color: #6c757d;
    }

    .qrcode-display-container {
    padding: 15px;
    background: #ffffff;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    }

    .qr-frame-container {
    max-width: 750px;
    margin: 0 auto;
    }

    #qrStatusFrame {
    min-height: 320px;
    width: 100%;
    border: 2px solid #28a745;
    border-radius: 8px;
    }

    /* Scan Info Container - Sisi Kanan */
    .scan-info-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    min-height: 450px;
    }

    .scan-info-card {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 15px;
    }

    .scan-info-table {
    margin-bottom: 0;
    }

    .scan-info-table .info-label {
    width: 45%;
    color: #495057;
    font-size: 14px;
    padding: 8px 5px;
    border-bottom: 1px solid #f1f1f1;
    }

    .scan-info-table .info-value {
    color: #212529;
    font-weight: 500;
    font-size: 14px;
    padding: 8px 5px;
    border-bottom: 1px solid #f1f1f1;
    }

    /* Status Indicator */
    .status-indicator .alert {
    margin-bottom: 0;
    font-size: 14px;
    padding: 10px 15px;
    }

    .auto-save-info .alert {
    margin-bottom: 0;
    padding: 10px 15px;
    }

    /* Info Links Styling */
    .info-links {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border: 1px solid #e9ecef;
    }

    .info-links .btn {
    font-size: 12px;
    padding: 4px 8px;
    }

    /* Log Container */
    .log-container {
    height: 300px;
    overflow-y: auto;
    background: #1e1e1e;
    padding: 15px;
    border-radius: 8px;
    font-family: 'Courier New', monospace;
    color: #ffffff;
    font-size: 13px;
    line-height: 1.4;
    }

    .log-entry {
    margin-bottom: 8px;
    padding: 4px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .timestamp {
    color: #ffc107;
    font-weight: bold;
    }

    .log-success {
    color: #28a745;
    }

    .log-error {
    color: #dc3545;
    }

    .log-warning {
    color: #ffc107;
    }

    .log-info {
    color: #17a2b8;
    }

    /* Card Enhancements */
    .card {
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: none;
    margin-bottom: 1rem;
    }

    .card.border-info {
    border: 1px solid #17a2b8 !important;
    }

    .card.border-primary {
    border: 1px solid #007bff !important;
    }

    .card.border-success {
    border: 1px solid #28a745 !important;
    }

    .card.border-secondary {
    border: 1px solid #6c757d !important;
    }

    /* Button Enhancements */
    .btn {
    transition: all 0.3s ease;
    border-radius: 6px;
    }

    .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-lg {
    padding: 12px 20px;
    font-size: 16px;
    font-weight: 500;
    }

    /* Confirmation Section Styling */
    .confirmation-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 25px;
    border-radius: 12px;
    margin: 20px 0;
    border: 2px solid #28a745;
    }

    .confirmation-section h5 {
    color: #155724;
    margin-bottom: 20px;
    }

    #confirmScanBtn {
    padding: 15px 25px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    transition: all 0.3s ease;
    }

    #confirmScanBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
    }

    #countdown-timer {
    font-size: 18px;
    font-weight: bold;
    color: #dc3545;
    }

    /* Responsive Design */
    @media (max-width: 768px) {

    .qr-scan-container,
    .scan-info-container {
      min-height: auto;
      margin-bottom: 20px;
    }

    .col-md-7,
    .col-md-5 {
      padding-right: 15px;
      padding-left: 15px;
    }

    .qrcode-qr-placeholder {
      width: 220px;
      height: 220px;
    }

    #qrStatusFrame {
      min-height: 250px;
    }
    }

    /* Loading Animation */
    .spinner-border {
    width: 3rem;
    height: 3rem;
    }

    /* Success/Error States */
    .bg-success-light {
    background-color: #d4edda !important;
    }

    .bg-warning-light {
    background-color: #fff3cd !important;
    }

    .bg-danger-light {
    background-color: #f8d7da !important;
    }

    .countdown-display {
      background: rgba(255, 255, 255, 0.9);
      border-radius: 8px;
      padding: 10px 15px;
      display: inline-block;
      border: 2px solid #28a745;
      margin: 5px 0;
    }

    #countdown-timer {
      font-family: 'Courier New', monospace;
      font-size: 24px !important;
      font-weight: bold;
      text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
      letter-spacing: 2px;
    }

    #countdown-timer.text-success {
      color: #28a745 !important;
    }

    #countdown-timer.text-warning {
      color: #ffc107 !important;
    }

    #countdown-timer.text-danger {
      color: #dc3545 !important;
      animation: blink 1s infinite;
    }

    @keyframes blink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0.5; }
    }

    /* Confirmation Section Enhancement */
    .confirmation-section {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 25px;
      border-radius: 12px;
      margin: 20px 0;
      border: 2px solid #28a745;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .confirmation-section .alert {
      border-radius: 8px;
      border: 1px solid #ffc107;
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function () {
    // ========================================
    // GLOBAL VARIABLES
    // ========================================

    let whatsappUrl = '{{ $whatsappUrl }}';
    let statusCheckInterval;
    let currentStatus = {
      running: false,
      authenticated: false,
      latest_scan: null
    };

    // Auto scan monitoring variablesF
    let autoScanAttempts = 0;
    const maxAutoScanAttempts = 10;
    let autoScanInterval;
    let monitoringActive = false;
    let confirmationTimer;
    let countdownInterval;

    // ========================================
    // INITIALIZATION
    // ========================================

    // Initialize on page load
    init();

    function init() {
      console.log('🚀 Initializing WhatsApp Management System...');

      // Start immediate status check
      checkStatus();
      updateQRCodeStatus();

      // Setup periodic monitoring
      setupPeriodicMonitoring();

      // Setup monitoring with delay for auto scan
      setTimeout(() => {
      startAutoScanMonitoring();
      }, 3000);

      console.log('✅ System initialized');
    }

    // ========================================
    // PERIODIC MONITORING SETUP
    // ========================================

    function setupPeriodicMonitoring() {
      // Clear existing intervals
      if (statusCheckInterval) {
      clearInterval(statusCheckInterval);
      }

      // Status check every 10 seconds
      statusCheckInterval = setInterval(() => {
      checkStatus();
      updateQRCodeStatus();
      }, 10000);

      console.log('📊 Periodic monitoring started');
    }

    function startAutoScanMonitoring() {
      if (monitoringActive) {
      console.log('⚠️ Auto scan monitoring already active');
      return;
      }

      monitoringActive = true;
      console.log('🔍 Starting auto scan monitoring...');

      // Initial auto scan check
      setTimeout(() => {
      checkForAutoScan();
      }, 2000);

      // Periodic auto scan check every 30 seconds
      autoScanInterval = setInterval(() => {
      checkForAutoScan();
      }, 30000);
    }

    // ========================================
    // STATUS CHECKING FUNCTIONS
    // ========================================

    function checkStatus() {
      $.get(`/${whatsappUrl}/status`)
      .done(function (response) {
        console.log('📊 Status response:', response);

        // Update current status
        currentStatus.running = response.running || false;
        currentStatus.authenticated = response.authenticated || false;

        // Update UI
        updateServerStatus(response.running, response.message);
        updateAuthStatus(response.authenticated);

      })
      .fail(function (xhr) {
        console.error('❌ Status check error:', xhr);
        updateServerStatus(false, 'Error checking status');
        updateAuthStatus(false);
        addLog('Gagal mengecek status server', 'error');

        // Reset current status on error
        currentStatus.running = false;
        currentStatus.authenticated = false;
      });
    }

    function updateQRCodeStatus() {
      $.get(`/${whatsappUrl}/qrcode-status`)
      .done(function (response) {
        console.log('🔍 QR Status Response:', response);

        if (response.success) {
        // Update current status with latest scan info
        currentStatus.latest_scan = response.latest_scan;

        // Render QR status UI
        renderQRCodeStatus(response);

        // Update scan info table
        updateScanInfoTable(response.latest_scan);

        // Log scan info if available
        if (response.latest_scan) {
          addLog('✅ Scan log ditemukan: ' + response.latest_scan.log_qrcode_wa_nomor_pengirim, 'success');
        }
        } else {
        addLog('⚠️ Error checking QR status: ' + (response.message || 'Unknown error'), 'error');
        }
      })
      .fail(function (xhr) {
        console.error('❌ QRCode status check error:', xhr);
        addLog('❌ Failed to check QR status', 'error');
      });
    }

    // ========================================
    // AUTO SCAN DETECTION FUNCTIONS
    // ========================================

    function checkForAutoScan() {
      console.log('🔍 Checking for auto scan opportunity...');

      // Hanya jalankan jika server running dan authenticated tapi belum ada scan log
      if (!currentStatus.running || !currentStatus.authenticated) {
      console.log('⏸️ Auto scan paused - server not ready');
      autoScanAttempts = 0;
      return;
      }

      // Jika sudah ada scan log, stop monitoring
      if (currentStatus.latest_scan) {
      console.log('✅ Auto scan monitoring stopped - scan log exists');
      autoScanAttempts = 0;
      return;
      }

      // Jika dalam batas percobaan, trigger auto scan
      if (autoScanAttempts < maxAutoScanAttempts) {
      autoScanAttempts++;
      console.log(`🎯 Auto scan attempt ${autoScanAttempts}/${maxAutoScanAttempts}`);

      // Langsung trigger tanpa cek connected phone dulu
      performAutoScanLog();
      } else {
      console.log('⏹️ Auto scan attempts exceeded');
      addLog('⚠️ Auto scan gagal setelah ' + maxAutoScanAttempts + ' percobaan', 'warning');
      autoScanAttempts = 0;
      }
    }

    function performAutoScanLog() {
      console.log('⚡ Performing auto scan log...');

      $.post(`/${whatsappUrl}/trigger-scan-log`)
      .done(function (triggerResponse) {
        console.log('✅ Trigger Response:', triggerResponse);

        if (triggerResponse.success) {
        addLog('✅ Auto scan log berhasil: ' + (triggerResponse.phone_number || 'auto'), 'success');

        // Reset attempts on success
        autoScanAttempts = 0;

        // Refresh status setelah berhasil
        setTimeout(() => {
          updateQRCodeStatus();
        }, 2000);
        } else {
        addLog('⚠️ Auto scan log failed: ' + triggerResponse.message, 'warning');

        // Retry dengan delay jika gagal
        if (autoScanAttempts < maxAutoScanAttempts) {
          setTimeout(() => {
          performAutoScanLog();
          }, 5000);
        }
        }
      })
      .fail(function (xhr) {
        console.error('❌ Trigger scan log error:', xhr);
        addLog('❌ Error triggering auto scan log', 'error');

        // Retry dengan delay jika error
        if (autoScanAttempts < maxAutoScanAttempts) {
        setTimeout(() => {
          performAutoScanLog();
        }, 5000);
        }
      });
    }

    // ========================================
    // SERVER CONTROL FUNCTIONS
    // ========================================

    function startServer(button) {
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memulai...');
      addLog('🚀 Memulai WhatsApp server...', 'info');

      $.post(`/${whatsappUrl}/start`)
      .done(function (response) {
        if (response.success) {
        addLog('✅ ' + response.message, 'success');
        addLog('📱 QR Code akan tersedia dalam beberapa detik...', 'info');

        // Check status after delay
        setTimeout(() => {
          checkStatus();
          updateQRCodeStatus();
        }, 5000);
        } else {
        addLog('❌ ' + response.message, 'error');
        }
      })
      .fail(function (xhr) {
        let errorMsg = '❌ Error: Gagal menjalankan server';
        if (xhr.responseJSON && xhr.responseJSON.message) {
        errorMsg += ' - ' + xhr.responseJSON.message;
        }
        addLog(errorMsg, 'error');
        console.error('Start server error:', xhr);
      })
      .always(function () {
        button.prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Mulai Server');
      });
    }

    function stopServer(button) {
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menghentikan...');
      addLog('🛑 Menghentikan WhatsApp server...', 'info');

      $.post(`/${whatsappUrl}/stop`)
      .done(function (response) {
        if (response.success) {
        addLog('✅ ' + response.message, 'success');

        // Reset status
        currentStatus.running = false;
        currentStatus.authenticated = false;
        currentStatus.latest_scan = null;
        autoScanAttempts = 0;

        // Clear scan info
        clearScanInfoTable();

        setTimeout(() => {
          checkStatus();
          updateQRCodeStatus();
        }, 2000);
        } else {
        addLog('❌ ' + response.message, 'error');
        }
      })
      .fail(function (xhr) {
        addLog('❌ Error: Gagal menghentikan server', 'error');
        console.error('Stop server error:', xhr);
      })
      .always(function () {
        button.prop('disabled', false).html('<i class="fas fa-stop mr-2"></i>Hentikan Server');
      });
    }

    function resetSession(button) {
      Swal.fire({
      title: 'Reset Sesi WhatsApp?',
      text: 'Nomor WhatsApp yang terhubung akan terputus dan Anda perlu scan QR code lagi.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ffc107',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Reset!',
      cancelButtonText: 'Batal'
      }).then((result) => {
      if (result.isConfirmed) {
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Mereset...');
        addLog('🔄 Mereset session WhatsApp...', 'warning');

        $.post(`/${whatsappUrl}/reset`)
        .done(function (response) {
          if (response.success) {
          addLog('✅ ' + response.message, 'success');

          // Reset all status
          currentStatus.running = false;
          currentStatus.authenticated = false;
          currentStatus.latest_scan = null;
          autoScanAttempts = 0;

          // Clear scan info
          clearScanInfoTable();

          setTimeout(() => {
            checkStatus();
            updateQRCodeStatus();
          }, 3000);
          } else {
          addLog('❌ ' + response.message, 'error');
          }
        })
        .fail(function (xhr) {
          addLog('❌ Error: Gagal reset session', 'error');
          console.error('Reset session error:', xhr);
        })
        .always(function () {
          button.prop('disabled', false).html('<i class="fas fa-refresh mr-2"></i>Reset Sesi');
        });
      }
      });
    }

    // ========================================
    // UI UPDATE FUNCTIONS
    // ========================================

    function updateServerStatus(running, message) {
      const statusBox = $('#serverStatus .info-box-icon');
      const statusText = $('#serverStatusText');

      if (running) {
      statusBox.removeClass('bg-secondary bg-danger').addClass('bg-success');
      statusBox.find('i').removeClass('fa-server fa-times-circle').addClass('fa-check-circle');
      statusText.text('Server Berjalan').removeClass('text-danger').addClass('text-success');
      } else {
      statusBox.removeClass('bg-secondary bg-success').addClass('bg-danger');
      statusBox.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
      statusText.text('Server Tidak Berjalan').removeClass('text-success').addClass('text-danger');
      }
    }

    function updateAuthStatus(authenticated) {
      const statusBox = $('#authStatus .info-box-icon');
      const statusText = $('#authStatusText');

      if (authenticated) {
      statusBox.removeClass('bg-secondary bg-warning').addClass('bg-success');
      statusBox.find('i').removeClass('fa-mobile-alt fa-exclamation-triangle').addClass('fa-check-circle');
      statusText.text('Terhubung').removeClass('text-warning').addClass('text-success');
      } else {
      statusBox.removeClass('bg-secondary bg-success').addClass('bg-warning');
      statusBox.find('i').removeClass('fa-check-circle').addClass('fa-exclamation-triangle');
      statusText.text('Belum Terhubung').removeClass('text-success').addClass('text-warning');
      }
    }

    function updateScanInfoTable(scanData) {
      if (scanData) {
      $('#info-nomor').text(scanData.log_qrcode_wa_nomor_pengirim || '-');
      $('#info-user').text(scanData.log_qrcode_wa_user_scan || '-');
      $('#info-hakakses').text(scanData.log_qrcode_wa_ha_scan || '-');
      $('#info-tanggal').text(scanData.log_qrcode_wa_tanggal_scan || '-');
      } else {
      clearScanInfoTable();
      }
    }

    function clearScanInfoTable() {
      $('#info-nomor').text('-');
      $('#info-user').text('-');
      $('#info-hakakses').text('-');
      $('#info-tanggal').text('-');
    }

    function addLog(message, type = 'info') {
      const logContainer = $('#logContainer');
      const timestamp = getCurrentTime();
      const logClass = `log-${type}`;

      const logEntry = `
      <div class="log-entry ${logClass}">
      <span class="timestamp">[${timestamp}]</span> 
      <i class="fas fa-${getLogIcon(type)} mr-1"></i>
      ${message}
      </div>
      `;

      logContainer.append(logEntry);
      logContainer.scrollTop(logContainer[0].scrollHeight);

      // Keep only last 50 log entries
      const entries = logContainer.find('.log-entry');
      if (entries.length > 50) {
      entries.first().remove();
      }
    }

    function getLogIcon(type) {
      switch (type) {
      case 'success': return 'check-circle';
      case 'error': return 'times-circle';
      case 'warning': return 'exclamation-triangle';
      case 'info': return 'info-circle';
      default: return 'info-circle';
      }
    }

    function getCurrentTime() {
      return new Date().toLocaleTimeString();
    }

    // ========================================
    // QR CODE STATUS RENDERING
    // ========================================

    function renderQRCodeStatus(data) {
      const container = $('#qrcodeStatusContent');
      const statusAlert = $('#statusAlert');
      const statusMessage = $('#statusMessage');
      let content = '';
      let alertClass = 'alert-info';
      let statusText = '';

      switch (data.status) {
      case 'server-belum-distart':
        content = generateServerNotStartedContent();
        alertClass = 'alert-warning';
        statusText = 'Server belum distart';
        break;

      case 'belum-terscan':
        content = generateNotScannedContent();
        alertClass = 'alert-info';
        statusText = 'Silakan scan QR Code dengan WhatsApp Anda';
        break;

      case 'authenticated-belum-log':
        content = generateAuthenticatedNoLogContent();
        alertClass = 'alert-warning';
        statusText = 'WhatsApp terhubung, memproses log otomatis...';
        break;

      case 'pending-confirmation':
        if (data.pending_scan) {
        content = generatePendingConfirmationContent(data.pending_scan);
        alertClass = 'alert-success';
        statusText = 'Scan berhasil! Klik tombol konfirmasi untuk melanjutkan';

        // Setup event listeners dan countdown setelah content di-render
        setTimeout(() => {
          setupConfirmationEventListeners();
          startConfirmationCountdown(data.pending_scan.confirmation_expires_at);
        }, 100);
        } else {
        content = generateAuthenticatedNoLogContent();
        alertClass = 'alert-warning';
        statusText = 'Memproses konfirmasi...';
        }
        break;

      case 'sudah-terscan':
        if (data.latest_scan) {
        content = generateScannedContent(data.latest_scan);
        alertClass = 'alert-success';
        statusText = 'WhatsApp siap digunakan!';
        }
        break;

      default:
        content = generateDefaultContent();
        alertClass = 'alert-secondary';
        statusText = 'Memuat status...';
        break;
      }

      container.html(content);

      // Update status alert
      statusAlert.removeClass('alert-info alert-warning alert-success alert-danger alert-secondary')
      .addClass(alertClass);
      statusMessage.html(`<i class="fas fa-${getStatusIcon(alertClass)} mr-2"></i>${statusText}`);

      // Setup event listeners dan countdown untuk pending confirmation
      if (data.status === 'pending-confirmation' && data.pending_scan) {
      setupConfirmationEventListeners();
      startConfirmationCountdown(data.pending_scan.confirmation_expires_at);
      }
    }

    function getStatusIcon(alertClass) {
      switch (alertClass) {
      case 'alert-success': return 'check-circle';
      case 'alert-warning': return 'exclamation-triangle';
      case 'alert-danger': return 'times-circle';
      case 'alert-info': return 'info-circle';
      default: return 'question-circle';
      }
    }

    // Function untuk setup event listeners konfirmasi
    function setupConfirmationEventListeners() {
      $(document).off('click', '#confirmScanBtn');
      $(document).on('click', '#confirmScanBtn', function () {
      const scanId = $(this).data('scan-id');
      const button = $(this);

      confirmScanLog(scanId, button);
      });
    }

    // Function untuk konfirmasi scan log
    function confirmScanLog(scanId, buttonElement) {
      console.log('🔵 Confirming scan log with ID:', scanId);

      buttonElement.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...');

      $.post(`/${whatsappUrl}/confirm-scan-log`, {
      scan_log_id: scanId,
      _token: $('meta[name="csrf-token"]').attr('content')
      })
      .done(function (response) {
        console.log('✅ Confirm scan log response:', response);

        if (response.success) {
        addLog('✅ Konfirmasi scan log berhasil', 'success');

        // Clear countdown timer
        if (confirmationTimer) {
          clearTimeout(confirmationTimer);
          confirmationTimer = null;
        }
        if (countdownInterval) {
          clearInterval(countdownInterval);
          countdownInterval = null;
        }

        // Reset auto scan attempts
        autoScanAttempts = 0;

        // Update status
        setTimeout(() => {
          updateQRCodeStatus();
        }, 1000);

        Swal.fire({
          title: 'Berhasil!',
          text: 'Konfirmasi identitas berhasil. WhatsApp siap digunakan!',
          icon: 'success',
          confirmButtonText: 'OK'
        });
        } else {
        addLog('❌ Gagal konfirmasi scan log: ' + response.message, 'error');
        buttonElement.prop('disabled', false).html('<i class="fas fa-hand-point-up mr-2"></i>Konfirmasi Identitas');

        Swal.fire({
          title: 'Gagal!',
          text: response.message || 'Gagal konfirmasi scan log',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        }
      })
      .fail(function (xhr) {
        console.error('❌ Confirm scan log error:', xhr);
        addLog('❌ Error konfirmasi scan log', 'error');
        buttonElement.prop('disabled', false).html('<i class="fas fa-hand-point-up mr-2"></i>Konfirmasi Identitas');

        Swal.fire({
        title: 'Error!',
        text: 'Terjadi kesalahan saat konfirmasi',
        icon: 'error',
        confirmButtonText: 'OK'
        });
      });
    }

    // Function untuk countdown timer
    function startConfirmationCountdown(expiresAt) {
      console.log('🕒 Starting countdown for:', expiresAt);

      // Clear existing timers terlebih dahulu
      if (confirmationTimer) {
      clearTimeout(confirmationTimer);
      confirmationTimer = null;
      }
      if (countdownInterval) {
      clearInterval(countdownInterval);
      countdownInterval = null;
      }

      // Parse expiry time dengan format yang benar
      let expiryTime;
      if (typeof expiresAt === 'string') {
      // Jika format string, parse dengan benar
      expiryTime = new Date(expiresAt.replace(' ', 'T')).getTime();
      } else {
      expiryTime = new Date(expiresAt).getTime();
      }

      console.log('Expiry time:', new Date(expiryTime));
      console.log('Current time:', new Date());

      const timerElement = $('#countdown-timer');

      // Pastikan elemen ada
      if (timerElement.length === 0) {
      console.error('❌ Countdown timer element not found');
      return;
      }

      // Function untuk update countdown
      function updateCountdown() {
      const now = new Date().getTime();
      const timeLeft = expiryTime - now;

      console.log('Time left (ms):', timeLeft);

      if (timeLeft <= 0) {
        // Time expired
        clearInterval(countdownInterval);
        timerElement.text('KADALUARSA').removeClass('text-warning text-success').addClass('text-danger');

        addLog('⏰ Waktu konfirmasi habis, session akan direset', 'warning');

        // Auto reset after expiry
        setTimeout(() => {
        resetExpiredScan();
        }, 2000);

        return;
      }

      // Calculate minutes and seconds left
      const minutes = Math.floor(timeLeft / (1000 * 60));
      const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

      const formattedTime = `${minutes}:${seconds.toString().padStart(2, '0')}`;
      timerElement.text(formattedTime);

      // Change color when time is running out
      if (timeLeft <= 60000) { // Last minute
        timerElement.removeClass('text-warning text-success').addClass('text-danger');
      } else if (timeLeft <= 120000) { // Last 2 minutes
        timerElement.removeClass('text-success text-danger').addClass('text-warning');
      } else {
        timerElement.removeClass('text-warning text-danger').addClass('text-success');
      }
      }

      // Initial update
      updateCountdown();

      // Update countdown every second
      countdownInterval = setInterval(updateCountdown, 1000);

      // Set timeout for automatic reset (backup)
      const timeoutDuration = expiryTime - new Date().getTime();
      if (timeoutDuration > 0) {
      confirmationTimer = setTimeout(() => {
        addLog('⏰ Session expired, melakukan reset otomatis...', 'warning');
        resetExpiredScan();
      }, timeoutDuration + 1000); // +1 detik untuk memastikan
      }

      console.log('✅ Countdown started successfully');
    }

    // Function untuk reset expired scan
    function resetExpiredScan() {
      console.log('🔄 Resetting expired scan...');

      // Clear timers
      if (confirmationTimer) {
      clearTimeout(confirmationTimer);
      confirmationTimer = null;
      }
      if (countdownInterval) {
      clearInterval(countdownInterval);
      countdownInterval = null;
      }

      addLog('🔄 Mereset session yang expired...', 'warning');

      $.post(`/${whatsappUrl}/reset-expired-scan`)
      .done(function (response) {
        if (response.success) {
        addLog('✅ ' + response.message, 'success');

        // Reset status
        currentStatus.running = false;
        currentStatus.authenticated = false;
        currentStatus.latest_scan = null;
        autoScanAttempts = 0;

        // Clear scan info
        clearScanInfoTable();

        setTimeout(() => {
          checkStatus();
          updateQRCodeStatus();
        }, 3000);
        } else {
        addLog('❌ Error reset expired scan: ' + response.message, 'error');
        }
      })
      .fail(function (xhr) {
        console.error('❌ Reset expired scan error:', xhr);
        addLog('❌ Error reset expired scan', 'error');
      });
    }

    // Update cleanup section
    $(window).on('beforeunload', function () {
      console.log('🧹 Cleaning up intervals and timers...');

      if (statusCheckInterval) {
      clearInterval(statusCheckInterval);
      }

      if (autoScanInterval) {
      clearInterval(autoScanInterval);
      }

      if (confirmationTimer) {
      clearTimeout(confirmationTimer);
      confirmationTimer = null;
      }

      if (countdownInterval) {
      clearInterval(countdownInterval);
      countdownInterval = null;
      }

      monitoringActive = false;
    });

    function generateServerNotStartedContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-warning-light">
      <div class="text-warning">
      <i class="fas fa-server fa-4x mb-3"></i>
      <br><strong>Server Belum Distart</strong>
      <br><small>Klik tombol "Mulai Server" untuk memulai</small>
      </div>
      </div>
      </div>
      `;
    }

    function generateNotScannedContent() {
      return `
      <div class="qrcode-status-content">
      <div style="text-align: center; margin-bottom: 15px;">
      <i class="fas fa-qrcode fa-3x text-success"></i>
      <h5 class="mt-2 text-success">QR Code Siap untuk Discan</h5>
      </div>
      <div class="qr-frame-container" style="text-align: center;">
      <iframe id="qrStatusFrame" src="http://localhost:3000/qr" width="100%" height="600" 
      style="border: 2px solid #28a745; border-radius: 8px; background: #f8f9fa;"></iframe>
      </div>
      <div class="mt-3">
      <small class="text-muted">
      <i class="fas fa-mobile-alt mr-1"></i>
      Buka WhatsApp di HP → Menu → WhatsApp Web → Scan QR Code di atas
      </small>
      <div class="mb-2">
      <strong>QR Code URL:</strong>
      <a href="http://localhost:3000/qr" target="_blank" class="btn btn-sm btn-outline-primary ml-2">
      <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000/qr
      </a>
      </div>
      </div>
      </div>
      `;
    }

    function generateAuthenticatedNoLogContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-warning-light">
      <div class="text-warning">
      <i class="fas fa-cog fa-spin fa-4x mb-3"></i>
      <br><strong>Memproses Otomatis</strong>
      <br><small>WhatsApp terhubung, sedang menyimpan log...</small>
      </div>
      </div>
      </div>
      `;
    }

    // Function untuk generate pending confirmation content - DIPERBAIKI
    function generatePendingConfirmationContent(pendingScan) {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-success-light">
      <div class="text-success">
      <i class="fas fa-check-circle fa-4x mb-3"></i>
      <br><strong>Scan Berhasil!</strong>
      <br><small>Nomor: ${pendingScan.log_qrcode_wa_nomor_pengirim}</small>
      </div>
      </div>

      <div class="confirmation-section mt-3">
      <h6 class="text-center mb-3 text-success">
      <i class="fas fa-user-check mr-2"></i>Konfirmasi Identitas
      </h6>

      <div class="alert alert-warning mb-3 text-center">
      <div class="mb-2">
      <i class="fas fa-clock mr-2"></i>
      <strong>Waktu tersisa:</strong>
      </div>
      <div class="countdown-display">
      <span id="countdown-timer" class="font-weight-bold text-success" style="font-size: 24px;">3:00</span>
      </div>
      <small class="d-block mt-2">
      Jika tidak dikonfirmasi dalam 3 menit setelah melakukan scan, koneksi akan terputus otomatis
      </small>
      </div>

      <div class="text-center mb-3">
      <button id="confirmScanBtn" class="btn btn-success btn-lg" 
      data-scan-id="${pendingScan.log_qrcode_wa_id}">
      <i class="fas fa-hand-point-up mr-2"></i>
      Konfirmasi Identitas
      </button>
      </div>

      <div class="text-center">
      <small class="text-muted">
      <i class="fas fa-info-circle mr-1"></i>
      Klik tombol ini untuk mengkonfirmasi bahwa Anda yang melakukan scan
      </small>
      </div>
      </div>
      </div>
      `;
    }

    function generateScannedContent(scanData) {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-success-light">
      <div class="text-success">
      <i class="fas fa-check-circle fa-4x mb-3"></i>
      <br><strong>WhatsApp Siap Digunakan!</strong>
      <br><small>Koneksi berhasil dan aktif</small>
      </div>
      </div>

      <div class="alert alert-success mt-3">
      <i class="fas fa-check-circle mr-2"></i>
      <strong>Status:</strong> WhatsApp terhubung dan siap digunakan untuk mengirim pesan
      </div>
      </div>
      `;
    }

    function generateDefaultContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder">
      <div class="text-muted">
      <div class="spinner-border mb-3" role="status">
      <span class="sr-only">Memuat...</span>
      </div>
      <br><strong>Memuat Status...</strong>
      <br><small>Sedang memeriksa status koneksi</small>
      </div>
      </div>
      </div>
      `;
    }

    // ========================================
    // EVENT HANDLERS
    // ========================================

    // Start Server Button
    $('#startServer').click(function () {
      startServer($(this));
    });

    // Stop Server Button
    $('#stopServer').click(function () {
      stopServer($(this));
    });

    // Reset Session Button
    $('#resetSession').click(function () {
      resetSession($(this));
    });

    // Refresh Status Button
    $('#refreshStatus').click(function () {
      const button = $(this);
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Memperbarui...');

      addLog('🔄 Manual refresh status...', 'info');
      checkStatus();
      updateQRCodeStatus();

      setTimeout(() => {
      button.prop('disabled', false).html('<i class="fas fa-sync mr-1"></i>Refresh Status');
      }, 1000);
    });

    // Clear Log Button
    $('#clearLog').click(function () {
      $('#logContainer').html(`
      <div class="log-entry text-muted">
      <span class="timestamp">[${getCurrentTime()}]</span>
      <i class="fas fa-trash mr-1"></i>
      Log berhasil dibersihkan...
      </div>
      `);
      addLog('🗑️ Log berhasil dibersihkan', 'info');
    });

    // ========================================
    // CLEANUP
    // ========================================

    // Cleanup intervals on page unload
    $(window).on('beforeunload', function () {
      console.log('🧹 Cleaning up intervals...');

      if (statusCheckInterval) {
      clearInterval(statusCheckInterval);
      }

      if (autoScanInterval) {
      clearInterval(autoScanInterval);
      }

      monitoringActive = false;
    });

    // Console log for debugging
    console.log('📋 WhatsApp Management System loaded successfully');
    console.log('🔧 Configuration:', {
      whatsappUrl: whatsappUrl,
      maxAutoScanAttempts: maxAutoScanAttempts,
      monitoringInterval: '10s',
      autoScanInterval: '30s'
    });
    });
  </script>
@endpush