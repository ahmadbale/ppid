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
    <!-- Server Status Card -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card" id="statusCard">
        <div class="card-header bg-light">
        <h5 class="card-title mb-0">
          <i class="fas fa-server mr-2"></i>Status Server WhatsApp
        </h5>
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-md-6">
          <div class="info-box" id="serverStatus">
            <span class="info-box-icon bg-secondary">
            <i class="fas fa-server"></i>
            </span>
            <div class="info-box-content">
            <span class="info-box-text">Server Status</span>
            <span class="info-box-number" id="serverStatusText">Checking...</span>
            </div>
          </div>
          </div>
          <div class="col-md-6">
          <div class="info-box" id="authStatus">
            <span class="info-box-icon bg-secondary">
            <i class="fas fa-mobile-alt"></i>
            </span>
            <div class="info-box-content">
            <span class="info-box-text">WhatsApp Status</span>
            <span class="info-box-number" id="authStatusText">Checking...</span>
            </div>
          </div>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Control Panel -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-light">
        <h5 class="card-title mb-0">
          <i class="fas fa-cogs mr-2"></i>Control Panel
        </h5>
        </div>
        <div class="card-body">
        <div class="row">
          <div class="col-md-4">
          <button class="btn btn-success btn-block" id="startServer">
            <i class="fas fa-play mr-2"></i>Start Server
          </button>
          </div>
          <div class="col-md-4">
          <button class="btn btn-danger btn-block" id="stopServer">
            <i class="fas fa-stop mr-2"></i>Stop Server
          </button>
          </div>
          <div class="col-md-4">
          <button class="btn btn-warning btn-block" id="resetSession">
            <i class="fas fa-refresh mr-2"></i>Reset Session
          </button>
          </div>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- QRCode Status Section -->
    <div class="row mb-4">
      <div class="col-md-12">
      <div class="card" id="qrcodeStatusCard">
        <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-qrcode mr-2"></i>Status QRCode WhatsApp
        </h5>
        </div>
        <div class="card-body" id="qrcodeStatusContent">
        <!-- Content will be loaded dynamically -->
        </div>
      </div>
      </div>
    </div>

    <!-- QR Code Section (Hidden by default) -->
    <div class="row mb-4" id="qrSection" style="display: none;">
      <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-success text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-qrcode mr-2"></i>QR Code WhatsApp
        </h5>
        </div>
        <div class="card-body text-center">
        <div id="qrContainer">
          <iframe id="qrFrame" src="" width="100%" height="300" style="border: none; border-radius: 8px;"></iframe>
        </div>
        <div class="mt-3">
          <div class="alert alert-info">
          <i class="fas fa-info-circle mr-2"></i>
          <strong>Langkah-langkah:</strong>
          <ol class="mb-0 mt-2">
            <li>Buka WhatsApp di HP Anda</li>
            <li>Tap menu (3 titik) → WhatsApp Web</li>
            <li>Scan QR code di atas</li>
            <li>Tunggu hingga terhubung</li>
          </ol>
          </div>
          <div class="mt-3">
          <label class="form-label">Nomor WhatsApp Anda:</label>
          <input type="text" id="nomorWhatsApp" class="form-control" placeholder="Contoh: 085802517862"
            maxlength="20">
          <button class="btn btn-primary mt-2" id="saveQRCodeScan">
            <i class="fas fa-save mr-1"></i>Simpan Log Scan
          </button>
          </div>
        </div>
        </div>
      </div>
      </div>
      <div class="col-md-6">
      <div class="card">
        <div class="card-header bg-info text-white">
        <h5 class="card-title mb-0">
          <i class="fas fa-info-circle mr-2"></i>Informasi Server
        </h5>
        </div>
        <div class="card-body">
        <table class="table table-borderless">
          <tr>
          <th width="40%">Server URL:</th>
          <td>
            <a href="http://localhost:3000" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000
            </a>
          </td>
          </tr>
          <tr>
          <th>QR Code URL:</th>
          <td>
            <a href="http://localhost:3000/qr" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000/qr
            </a>
          </td>
          </tr>
          <tr>
          <th>API Status:</th>
          <td>
            <a href="http://localhost:3000/api/status" target="_blank" class="btn btn-sm btn-outline-primary">
            <i class="fas fa-external-link-alt mr-1"></i>http://localhost:3000/api/status
            </a>
          </td>
          </tr>
        </table>

        <div class="alert alert-warning mt-3">
          <i class="fas fa-exclamation-triangle mr-2"></i>
          <strong>Perhatian:</strong> Pastikan port 3000 tidak digunakan oleh aplikasi lain
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Activity Log -->
    <div class="row">
      <div class="col-md-12">
      <div class="card">
        <div class="card-header bg-light">
        <h5 class="card-title mb-0">
          <i class="fas fa-terminal mr-2"></i>Activity Log
        </h5>
        <div class="card-tools">
          <button id="clearLog" class="btn btn-sm btn-secondary">
          <i class="fas fa-trash mr-1"></i>Clear Log
          </button>
        </div>
        </div>
        <div class="card-body">
        <div id="logContainer"
          style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace;">
          <div class="log-entry text-muted">
          <span class="timestamp">[{{ date('H:i:s') }}]</span>
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
    .info-box {
    transition: all 0.3s ease;
    }

    .info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .log-entry {
    margin-bottom: 5px;
    padding: 2px 0;
    border-bottom: 1px solid #e9ecef;
    }

    .timestamp {
    color: #6c757d;
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

    .card {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border: none;
    }

    .btn {
    transition: all 0.3s ease;
    }

    .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    #qrFrame {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .qrcode-status-content {
    text-align: center;
    padding: 20px;
    }

    .qrcode-qr-placeholder {
    width: 200px;
    height: 200px;
    border: 2px dashed #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    border-radius: 8px;
    background: #f8f9fa;
    }

    .qrcode-display-container {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #dee2e6;
    }

    .qr-frame-container {
    max-width: 400px;
    margin: 0 auto;
    }

    #qrStatusFrame {
    min-height: 350px;
    width: 100%;
    max-width: 400px;
    }

    .scan-info-table {
    max-width: 500px;
    margin: 20px auto 0;
    }

    .scan-info-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
    }

    .scan-info-table td {
    background: #ffffff;
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

    // Auto scan monitoring variables
    let autoScanAttempts = 0;
    const maxAutoScanAttempts = 10;
    let autoScanInterval;
    let monitoringActive = false;

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

        // Handle QR section visibility
        handleQRSectionVisibility(response);

      })
      .fail(function (xhr) {
        console.error('❌ Status check error:', xhr);
        updateServerStatus(false, 'Error checking status');
        updateAuthStatus(false);
        hideQRSection();
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

    function triggerAutoScanLog() {
      console.log('🚀 Triggering auto scan log...');

      // First check connected phone
      $.get(`/${whatsappUrl}/connected-phone`)
      .done(function (phoneResponse) {
        console.log('📱 Connected Phone Response:', phoneResponse);

        if (phoneResponse.success && phoneResponse.connected_phone) {
        console.log('📞 Detected connected phone:', phoneResponse.connected_phone);

        // Trigger automatic scan log
        performAutoScanLog();
        } else {
        console.log('📞 No connected phone detected, will retry...');
        addLog('📞 Menunggu deteksi nomor WhatsApp...', 'info');

        // Retry after delay
        setTimeout(() => {
          if (autoScanAttempts < maxAutoScanAttempts) {
          triggerAutoScanLog();
          }
        }, 5000);
        }
      })
      .fail(function (xhr) {
        console.error('❌ Connected phone check error:', xhr);
        addLog('❌ Error checking connected phone', 'error');
      });
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
        button.prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Start Server');
      });
    }

    function stopServer(button) {
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Menghentikan...');
      addLog('🛑 Menghentikan WhatsApp server...', 'info');

      $.post(`/${whatsappUrl}/stop`)
      .done(function (response) {
        if (response.success) {
        addLog('✅ ' + response.message, 'success');
        hideQRSection();

        // Reset status
        currentStatus.running = false;
        currentStatus.authenticated = false;
        currentStatus.latest_scan = null;
        autoScanAttempts = 0;

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
        button.prop('disabled', false).html('<i class="fas fa-stop mr-2"></i>Stop Server');
      });
    }

    function resetSession(button) {
      Swal.fire({
      title: 'Reset Session WhatsApp?',
      text: 'Nomor WhatsApp yang terhubung akan terputus dan Anda perlu scan QR code lagi.',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#ffc107',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Ya, Reset!',
      cancelButtonText: 'Batal'
      }).then((result) => {
      if (result.isConfirmed) {
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Resetting...');
        addLog('🔄 Mereset session WhatsApp...', 'warning');

        $.post(`/${whatsappUrl}/reset`)
        .done(function (response) {
          if (response.success) {
          addLog('✅ ' + response.message, 'success');
          hideQRSection();

          // Reset all status
          currentStatus.running = false;
          currentStatus.authenticated = false;
          currentStatus.latest_scan = null;
          autoScanAttempts = 0;

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
          button.prop('disabled', false).html('<i class="fas fa-refresh mr-2"></i>Reset Session');
        });
      }
      });
    }

    // ========================================
    // MANUAL SAVE FUNCTIONS
    // ========================================

    // function saveQRCodeScanLog(inputSelector, buttonElement) {
    //   const nomorWA = $(inputSelector).val().trim();

    //   if (!nomorWA) {
    //   Swal.fire({
    //     title: 'Error!',
    //     text: 'Nomor WhatsApp harus diisi',
    //     icon: 'error',
    //     confirmButtonText: 'OK'
    //   });
    //   return;
    //   }

    //   console.log('💾 Attempting to save QR code log with nomor:', nomorWA);

    //   buttonElement.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

    //   $.post(`/${whatsappUrl}/save-qrcode-log`, {
    //   nomor_pengirim: nomorWA,
    //   _token: $('meta[name="csrf-token"]').attr('content')
    //   })
    //   .done(function (response) {
    //     console.log('💾 Save QR code log response:', response);

    //     if (response.success) {
    //     addLog('✅ Log scan QRCode berhasil disimpan', 'success');
    //     $(inputSelector).val('');

    //     // Reset auto scan attempts on successful manual save
    //     autoScanAttempts = 0;

    //     // Update status
    //     updateQRCodeStatus();

    //     Swal.fire({
    //       title: 'Berhasil!',
    //       text: 'Log scan QRCode berhasil disimpan',
    //       icon: 'success',
    //       confirmButtonText: 'OK'
    //     }).then(() => {
    //       setTimeout(() => {
    //       updateQRCodeStatus();
    //       }, 1000);
    //     });
    //     } else {
    //     addLog('❌ Gagal menyimpan log scan: ' + (response.message || 'Unknown error'), 'error');
    //     Swal.fire({
    //       title: 'Gagal!',
    //       text: response.message || 'Gagal menyimpan log scan',
    //       icon: 'error',
    //       confirmButtonText: 'OK'
    //     });
    //     }
    //   })
    //   .fail(function (xhr) {
    //     console.error('❌ Save QRCode log error:', xhr);

    //     let errorMessage = '❌ Error: Gagal menyimpan log scan';
    //     if (xhr.responseJSON && xhr.responseJSON.message) {
    //     errorMessage += ' - ' + xhr.responseJSON.message;
    //     }

    //     addLog(errorMessage, 'error');

    //     Swal.fire({
    //     title: 'Error!',
    //     text: errorMessage,
    //     icon: 'error',
    //     confirmButtonText: 'OK'
    //     });
    //   })
    //   .always(function () {
    //     buttonElement.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Log Scan');
    //   });
    // }

    // ========================================
    // UI UPDATE FUNCTIONS
    // ========================================

    function handleQRSectionVisibility(response) {
      if (response.running && !response.authenticated) {
      showQRSection();
      if (response.qr_available) {
        addLog('📱 Server berjalan, QR Code tersedia untuk scan', 'info');
      } else {
        addLog('⏳ Server berjalan, menunggu QR Code...', 'info');
      }
      } else if (response.authenticated) {
      hideQRSection();
      addLog('✅ WhatsApp sudah ter-authenticate', 'success');
      } else {
      hideQRSection();
      if (!response.running) {
        addLog('⚠️ Server tidak berjalan', 'warning');
      }
      }
    }

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

    function showQRSection() {
      $('#qrSection').slideDown();
      // Add timestamp to force iframe reload
      const timestamp = new Date().getTime();
      $('#qrFrame').attr('src', `http://localhost:3000/qr?t=${timestamp}`);
      addLog('📱 QR Code tersedia untuk scan', 'info');
    }

    function hideQRSection() {
      $('#qrSection').slideUp();
      $('#qrFrame').attr('src', '');
    }

    function addLog(message, type = 'info') {
      const logContainer = $('#logContainer');
      const timestamp = getCurrentTime();
      const logClass = `log-${type}`;

      const logEntry = `
      <div class="log-entry ${logClass}">
      <span class="timestamp">[${timestamp}]</span> ${message}
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

    function getCurrentTime() {
      return new Date().toLocaleTimeString();
    }

    // ========================================
    // QR CODE STATUS RENDERING
    // ========================================

    function renderQRCodeStatus(data) {
      const container = $('#qrcodeStatusContent');
      let content = '';

      switch (data.status) {
      case 'server-belum-distart':
        content = generateServerNotStartedContent();
        break;

      case 'belum-terscan':
        content = generateNotScannedContent();
        break;

      case 'authenticated-belum-log':
        content = generateAuthenticatedNoLogContent();
        break;

      case 'sudah-terscan':
        if (data.latest_scan) {
        content = generateScannedContent(data.latest_scan);
        }
        break;

      default:
        content = generateDefaultContent();
        break;
      }

      container.html(content);

      // Setup event listeners after content is rendered
      // setupQRStatusEventListeners();
    }

    function generateServerNotStartedContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder">
      <div class="text-muted">
      <i class="fas fa-server fa-3x mb-2"></i>
      <br>Server Belum Distart
      </div>
      </div>
      <div class="alert alert-info">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Status:</strong> Server belum distart
      <br>
      <small>Mulai server untuk melihat QRCode status</small>
      </div>
      </div>
      `;
    }

    function generateNotScannedContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-display-container" style="margin-bottom: 20px;">
      <div style="text-align: center; margin-bottom: 15px;">
      <i class="fas fa-qrcode fa-2x text-warning"></i>
      <h5 class="mt-2">Silahkan Scan QR Code</h5>
      </div>
      <div class="qr-frame-container" style="text-align: center;">
      <iframe id="qrStatusFrame" src="http://localhost:3000/qr" width="100%" height="350" 
      style="border: 2px solid #ffc107; border-radius: 8px; background: #f8f9fa;"></iframe>
      </div>
      <div class="alert alert-info mt-3">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Auto Save:</strong> Log scan akan otomatis tersimpan setelah QR Code berhasil discan.
      </div>
      </div>
      ${generateScanInfoTable()}
      </div>
    `;
    }

    function generateAuthenticatedNoLogContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-warning text-white">
      <div>
      <i class="fas fa-exclamation-triangle fa-3x mb-2"></i>
      <br>Terhubung - Auto Processing
      </div>
      </div>
      <div class="alert alert-warning">
      <i class="fas fa-exclamation-triangle mr-2"></i>
      <strong>Status:</strong> WhatsApp sudah terhubung, sistem sedang memproses auto save log...
      </div>
      <div class="alert alert-info">
      <i class="fas fa-cog fa-spin mr-2"></i>
      <strong>Proses Otomatis:</strong> Tunggu beberapa saat, log scan akan tersimpan secara otomatis.
      </div>
      ${generateScanInfoTable()}
      </div>
    `;
    }

    function generateScannedContent(scanData) {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder bg-success text-white">
      <div>
      <i class="fas fa-check-circle fa-3x mb-2"></i>
      <br>Sudah Terscan
      </div>
      </div>
      <div class="alert alert-success">
      <i class="fas fa-check-circle mr-2"></i>
      <strong>Status:</strong> WhatsApp sudah terhubung dan siap digunakan
      </div>
      <table class="table table-bordered scan-info-table">
      <tr>
      <th width="40%">Nomor WA Pengirim:</th>
      <td>${scanData.log_qrcode_wa_nomor_pengirim}</td>
      </tr>
      <tr>
      <th>Nama User yang melakukan Scan:</th>
      <td>${scanData.log_qrcode_wa_user_scan}</td>
      </tr>
      <tr>
      <th>Hak Akses User yang melakukan Scan:</th>
      <td>${scanData.log_qrcode_wa_ha_scan}</td>
      </tr>
      <tr>
      <th>Tanggal User Melakukan Scan:</th>
      <td>${scanData.log_qrcode_wa_tanggal_scan}</td>
      </tr>
      </table>
      </div>
      `;
    }

    function generateDefaultContent() {
      return `
      <div class="qrcode-status-content">
      <div class="qrcode-qr-placeholder">
      <div class="text-muted">
      <i class="fas fa-question fa-3x mb-2"></i>
      <br>Status Tidak Diketahui
      </div>
      </div>
      <div class="alert alert-secondary">
      <i class="fas fa-info-circle mr-2"></i>
      <strong>Status:</strong> Sedang memuat status...
      </div>
      </div>
      `;
    }

    function generateScanInfoTable() {
      return `
      <table class="table table-bordered scan-info-table">
      <tr>
      <th width="40%">Nomor WA Pengirim:</th>
      <td>-</td>
      </tr>
      <tr>
      <th>Nama User yang melakukan Scan:</th>
      <td>-</td>
      </tr>
      <tr>
      <th>Hak Akses User yang melakukan Scan:</th>
      <td>-</td>
      </tr>
      <tr>
      <th>Tanggal User Melakukan Scan:</th>
      <td>-</td>
      </tr>
      </table>
      `;
    }

    // function setupQRStatusEventListeners() {
    //   // Remove existing event listeners to prevent duplicates
    //   $(document).off('click', '#saveQRCodeScanStatus');
    //   $(document).off('click', '#saveQRCodeScanStatusAuth');

    //   // Setup new event listeners
    //   $(document).on('click', '#saveQRCodeScanStatus', function () {
    //   saveQRCodeScanLog('#nomorWhatsAppStatus', $(this));
    //   });

    //   $(document).on('click', '#saveQRCodeScanStatusAuth', function () {
    //   saveQRCodeScanLog('#nomorWhatsAppStatusAuth', $(this));
    //   });
    // }

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

    // Save QRCode Scan Log (Legacy - for main QR section)
    $(document).on('click', '#saveQRCodeScan', function () {
      const nomorWA = $('#nomorWhatsApp').val().trim();

      if (!nomorWA) {
      Swal.fire({
        title: 'Error!',
        text: 'Nomor WhatsApp harus diisi',
        icon: 'error',
        confirmButtonText: 'OK'
      });
      return;
      }

      const button = $(this);
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Menyimpan...');

      $.post(`/${whatsappUrl}/save-qrcode-log`, {
      nomor_pengirim: nomorWA,
      _token: $('meta[name="csrf-token"]').attr('content')
      })
      .done(function (response) {
        if (response.success) {
        addLog('✅ Log scan QRCode berhasil disimpan', 'success');
        $('#nomorWhatsApp').val('');
        hideQRSection();
        updateQRCodeStatus();

        Swal.fire({
          title: 'Berhasil!',
          text: 'Log scan QRCode berhasil disimpan',
          icon: 'success',
          confirmButtonText: 'OK'
        });
        } else {
        addLog('❌ Gagal menyimpan log scan: ' + response.message, 'error');
        }
      })
      .fail(function (xhr) {
        addLog('❌ Error: Gagal menyimpan log scan', 'error');
        console.error('Save QRCode log error:', xhr);
      })
      .always(function () {
        button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i>Simpan Log Scan');
      });
    });

    // Refresh Status Button
    $('#refreshStatus').click(function () {
      const button = $(this);
      button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...');

      addLog('🔄 Manual refresh status...', 'info');
      checkStatus();
      updateQRCodeStatus();

      setTimeout(() => {
      button.prop('disabled', false).html('<i class="fas fa-sync mr-1"></i>Refresh Status');
      }, 1000);
    });

    // Clear Log Button
    $('#clearLog').click(function () {
      $('#logContainer').html('<div class="log-entry text-muted"><span class="timestamp">[' + getCurrentTime() + ']</span> 🗑️ Log cleared...</div>');
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