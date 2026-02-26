<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\AdminWeb\MenuManagement\WebMenuUrl\detail.blade.php -->
<div class="modal-header">
    <h5 class="modal-title">{{ $title }}</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  
  <div class="modal-body">
    <style>
      .badge-pk {
        background-color: #007bff;
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
      }
      
      .badge-fk {
        background-color: #28a745;
        color: white;
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
      }
      
      .field-config-detail-table {
        font-size: 0.875rem;
      }
      
      .field-config-detail-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        white-space: nowrap;
      }
      
      .field-config-detail-table td {
        vertical-align: middle;
      }
    </style>

    <!-- Informasi Umum -->
    <div class="card mb-3">
      <div class="card-header bg-primary text-white">
        <strong><i class="fas fa-info-circle mr-1"></i> Informasi Umum</strong>
      </div>
      <div class="card-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th width="200">Aplikasi</th>
            <td>{{ $webMenuUrl->application ? $webMenuUrl->application->app_nama : 'Tidak Ada' }}</td>
          </tr>
          <tr>
            <th>Kategori Menu</th>
            <td>
              @if($webMenuUrl->wmu_kategori_menu === 'master')
                <span class="badge badge-success">Master</span>
              @elseif($webMenuUrl->wmu_kategori_menu === 'custom')
                <span class="badge badge-info">Custom</span>
              @elseif($webMenuUrl->wmu_kategori_menu === 'pengajuan')
                <span class="badge badge-warning">Pengajuan</span>
              @else
                <span class="badge badge-secondary">{{ ucfirst($webMenuUrl->wmu_kategori_menu) }}</span>
              @endif
            </td>
          </tr>
          <tr>
            <th>URL Menu</th>
            <td><code>{{ $webMenuUrl->wmu_nama }}</code></td>
          </tr>
          <tr>
            <th>Deskripsi</th>
            <td>{{ $webMenuUrl->wmu_keterangan ?: '-' }}</td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Informasi Controller (untuk Custom) -->
    @if($webMenuUrl->wmu_kategori_menu === 'custom')
    <div class="card mb-3">
      <div class="card-header bg-info text-white">
        <strong><i class="fas fa-code mr-1"></i> Konfigurasi Custom</strong>
      </div>
      <div class="card-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th width="200">Controller Name</th>
            <td><code>{{ $webMenuUrl->controller_name ?: '-' }}</code></td>
          </tr>
          <tr>
            <th>Module Type</th>
            <td>
              @if($webMenuUrl->module_type === 'sisfo')
                <span class="badge badge-primary">Sisfo</span>
              @elseif($webMenuUrl->module_type === 'user')
                <span class="badge badge-secondary">User</span>
              @else
                {{ ucfirst($webMenuUrl->module_type) }}
              @endif
            </td>
          </tr>
        </table>
      </div>
    </div>
    @endif

    <!-- Informasi Master (untuk Master) -->
    @if($webMenuUrl->wmu_kategori_menu === 'master')
    <div class="card mb-3">
      <div class="card-header bg-success text-white">
        <strong><i class="fas fa-table mr-1"></i> Konfigurasi Master</strong>
      </div>
      <div class="card-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th width="200">Tabel Akses</th>
            <td><code>{{ $webMenuUrl->wmu_akses_tabel ?: '-' }}</code></td>
          </tr>
          <tr>
            <th>Controller Name</th>
            <td><code>{{ $webMenuUrl->controller_name ?: 'Template\MasterController' }}</code></td>
          </tr>
        </table>
      </div>
    </div>

    <!-- Field Configurations -->
    @if($webMenuUrl->fieldConfigs && count($webMenuUrl->fieldConfigs) > 0)
    <div class="card mb-3">
      <div class="card-header bg-success text-white">
        <strong><i class="fas fa-list mr-1"></i> Konfigurasi Field ({{ count($webMenuUrl->fieldConfigs) }} Field)</strong>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-striped table-hover field-config-detail-table mb-0">
            <thead>
              <tr>
                <th class="text-center" width="40">No</th>
                <th>Kolom Database</th>
                <th>Label Field</th>
                <th>Type Input</th>
                <th>FK Config</th>
                <th>Kriteria</th>
                <th>Validasi</th>
                <th class="text-center">Visible</th>
                <th class="text-center">Tampil List</th>
                <th>Label Keterangan</th>
                <th class="text-center">Ukuran Max</th>
              </tr>
            </thead>
            <tbody>
              @foreach($webMenuUrl->fieldConfigs as $index => $field)
              <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                  <strong>{{ $field->wmfc_column_name }}</strong>
                  @if($field->wmfc_is_primary_key)
                    <span class="badge badge-pk ml-1">PK</span>
                  @endif
                  @if($field->wmfc_fk_table)
                    <span class="badge badge-fk ml-1">FK</span>
                  @endif
                  <br>
                  <small class="text-muted">{{ $field->wmfc_column_type }}</small>
                </td>
                <td>{{ $field->wmfc_field_label }}</td>
                <td>
                  <span class="badge badge-secondary">{{ $field->wmfc_field_type }}</span>
                </td>
                <td>
                  @if($field->wmfc_fk_table)
                    <small><code>{{ $field->wmfc_fk_table }}</code></small>
                    @if($field->wmfc_fk_display_columns)
                      @php
                        $fkCols = is_string($field->wmfc_fk_display_columns) ? json_decode($field->wmfc_fk_display_columns, true) : $field->wmfc_fk_display_columns;
                      @endphp
                      @if(is_array($fkCols) && count($fkCols))
                        <br><small class="text-muted">{{ implode(', ', $fkCols) }}</small>
                      @endif
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @php
                    $criteria = is_string($field->wmfc_criteria) ? json_decode($field->wmfc_criteria, true) : $field->wmfc_criteria;
                  @endphp
                  @if($criteria && is_array($criteria) && count($criteria) > 0)
                    @if(isset($criteria['case']) && $criteria['case'] === 'uppercase')
                      <span class="badge badge-info">Uppercase</span>
                    @endif
                    @if(isset($criteria['case']) && $criteria['case'] === 'lowercase')
                      <span class="badge badge-info">Lowercase</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td>
                  @php
                    $validation = is_string($field->wmfc_validation) ? json_decode($field->wmfc_validation, true) : $field->wmfc_validation;
                  @endphp
                  @if($validation && is_array($validation) && count($validation) > 0)
                    @if(isset($validation['required']) && $validation['required'])
                      <span class="badge badge-danger">Required</span>
                    @endif
                    @if(isset($validation['unique']) && $validation['unique'])
                      <span class="badge badge-warning">Unique</span>
                    @endif
                    @if(isset($validation['max']))
                      <span class="badge badge-secondary">Max: {{ $validation['max'] }}</span>
                    @endif
                    @if(isset($validation['min']))
                      <span class="badge badge-secondary">Min: {{ $validation['min'] }}</span>
                    @endif
                    @if(isset($validation['email']) && $validation['email'])
                      <span class="badge badge-primary">Email</span>
                    @endif
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if($field->wmfc_is_visible)
                    <i class="fas fa-check text-success"></i>
                  @else
                    <i class="fas fa-times text-danger"></i>
                  @endif
                </td>
                <td class="text-center">
                  @if(isset($field->wmfc_display_list) ? $field->wmfc_display_list : true)
                    <i class="fas fa-check text-success"></i>
                  @else
                    <i class="fas fa-times text-danger"></i>
                  @endif
                </td>
                <td>
                  @if(!empty($field->wmfc_label_keterangan))
                    <small>{{ $field->wmfc_label_keterangan }}</small>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
                <td class="text-center">
                  @if(!empty($field->wmfc_ukuran_max))
                    <span class="badge badge-info">{{ $field->wmfc_ukuran_max }} MB</span>
                  @else
                    <span class="text-muted">-</span>
                  @endif
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
    @endif
    @endif

    <!-- Informasi Audit -->
    <div class="card">
      <div class="card-header bg-secondary text-white">
        <strong><i class="fas fa-history mr-1"></i> Informasi Audit</strong>
      </div>
      <div class="card-body">
        <table class="table table-borderless mb-0">
          <tr>
            <th width="200">Tanggal Dibuat</th>
            <td>{{ date('d-m-Y H:i:s', strtotime($webMenuUrl->created_at)) }}</td>
          </tr>
          <tr>
            <th>Dibuat Oleh</th>
            <td>{{ $webMenuUrl->created_by }}</td>
          </tr>
          @if($webMenuUrl->updated_by)
            <tr>
              <th>Terakhir Diperbarui</th>
              <td>{{ date('d-m-Y H:i:s', strtotime($webMenuUrl->updated_at)) }}</td>
            </tr>
            <tr>
              <th>Diperbarui Oleh</th>
              <td>{{ $webMenuUrl->updated_by }}</td>
            </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  
  <div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
  </div>