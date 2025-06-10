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

      <!-- QR Code Section -->
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
              <div id="logContainer" style="height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: 'Courier New', monospace;">
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
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      border: none;
    }

    .btn {
      transition: all 0.3s ease;
    }

    .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    #qrFrame {
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
  </style>
@endpush

@push('js')
  <script>
    $(document).ready(function() {
      let whatsappUrl = '{{ $whatsappUrl }}';
      let statusCheckInterval;

      // Check status on page load
      checkStatus();
      
      // Auto refresh status every 10 seconds
      statusCheckInterval = setInterval(checkStatus, 10000);

      // Start Server
      $('#startServer').click(function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Starting...');
        
        addLog('Memulai WhatsApp server...', 'info');
        
        $.post(`/${whatsappUrl}/start`)
          .done(function(response) {
            if (response.success) {
              addLog(response.message, 'success');
              setTimeout(() => {
                checkStatus();
                if (response.qr_url) {
                  showQRSection();
                }
              }, 3000);
            } else {
              addLog(response.message, 'error');
            }
          })
          .fail(function(xhr) {
            addLog('Error: Gagal menjalankan server', 'error');
            console.error('Start server error:', xhr);
          })
          .always(function() {
            button.prop('disabled', false).html('<i class="fas fa-play mr-2"></i>Start Server');
          });
      });

      // Stop Server
      $('#stopServer').click(function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Stopping...');
        
        addLog('Menghentikan WhatsApp server...', 'info');
        
        $.post(`/${whatsappUrl}/stop`)
          .done(function(response) {
            if (response.success) {
              addLog(response.message, 'success');
              hideQRSection();
              setTimeout(checkStatus, 2000);
            } else {
              addLog(response.message, 'error');
            }
          })
          .fail(function(xhr) {
            addLog('Error: Gagal menghentikan server', 'error');
            console.error('Stop server error:', xhr);
          })
          .always(function() {
            button.prop('disabled', false).html('<i class="fas fa-stop mr-2"></i>Stop Server');
          });
      });

      // Reset Session
      $('#resetSession').click(function() {
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
            const button = $(this);
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Resetting...');
            
            addLog('Mereset session WhatsApp...', 'warning');
            
            $.post(`/${whatsappUrl}/reset`)
              .done(function(response) {
                if (response.success) {
                  addLog(response.message, 'success');
                  hideQRSection();
                  setTimeout(checkStatus, 3000);
                } else {
                  addLog(response.message, 'error');
                }
              })
              .fail(function(xhr) {
                addLog('Error: Gagal reset session', 'error');
                console.error('Reset session error:', xhr);
              })
              .always(function() {
                button.prop('disabled', false).html('<i class="fas fa-refresh mr-2"></i>Reset Session');
              });
          }
        });
      });

      // Refresh Status
      $('#refreshStatus').click(function() {
        const button = $(this);
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>Refreshing...');
        
        checkStatus();
        
        setTimeout(() => {
          button.prop('disabled', false).html('<i class="fas fa-sync mr-1"></i>Refresh Status');
        }, 1000);
      });

      // Clear Log
      $('#clearLog').click(function() {
        $('#logContainer').html('<div class="log-entry text-muted"><span class="timestamp">[' + getCurrentTime() + ']</span> Log cleared...</div>');
      });

      function checkStatus() {
        $.get(`/${whatsappUrl}/status`)
          .done(function(response) {
            updateServerStatus(response.running, response.message);
            updateAuthStatus(response.authenticated);
            
            if (response.running && !response.authenticated) {
              showQRSection();
            } else if (response.authenticated) {
              hideQRSection();
              addLog('WhatsApp sudah ter-authenticate', 'success');
            } else {
              hideQRSection();
            }
          })
          .fail(function(xhr) {
            updateServerStatus(false, 'Error checking status');
            updateAuthStatus(false);
            console.error('Status check error:', xhr);
          });
      }

      function updateServerStatus(running, message) {
        const statusBox = $('#serverStatus .info-box-icon');
        const statusText = $('#serverStatusText');
        
        if (running) {
          statusBox.removeClass('bg-secondary bg-danger').addClass('bg-success');
          statusBox.find('i').removeClass('fa-server').addClass('fa-check-circle');
          statusText.text('Server Running').removeClass('text-danger').addClass('text-success');
        } else {
          statusBox.removeClass('bg-secondary bg-success').addClass('bg-danger');
          statusBox.find('i').removeClass('fa-check-circle').addClass('fa-times-circle');
          statusText.text('Server Stopped').removeClass('text-success').addClass('text-danger');
        }
      }

      function updateAuthStatus(authenticated) {
        const statusBox = $('#authStatus .info-box-icon');
        const statusText = $('#authStatusText');
        
        if (authenticated) {
          statusBox.removeClass('bg-secondary bg-warning').addClass('bg-success');
          statusBox.find('i').removeClass('fa-mobile-alt').addClass('fa-check-circle');
          statusText.text('Connected').removeClass('text-warning').addClass('text-success');
        } else {
          statusBox.removeClass('bg-secondary bg-success').addClass('bg-warning');
          statusBox.find('i').removeClass('fa-check-circle').addClass('fa-exclamation-triangle');
          statusText.text('Not Connected').removeClass('text-success').addClass('text-warning');
        }
      }

      function showQRSection() {
        $('#qrSection').slideDown();
        $('#qrFrame').attr('src', 'http://localhost:3000/qr?' + new Date().getTime());
        addLog('QR Code tersedia untuk scan', 'info');
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

      // Cleanup interval on page unload
      $(window).on('beforeunload', function() {
        if (statusCheckInterval) {
          clearInterval(statusCheckInterval);
        }
      });
    });
  </script>
@endpush