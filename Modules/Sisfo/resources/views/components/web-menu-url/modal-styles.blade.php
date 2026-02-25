{{-- 
  Component: Modal Styles for Web Menu URL
  Shared CSS untuk create.blade.php dan update.blade.php
--}}
<style>
  /* ===== MODAL RESPONSIVE ===== */
  .modal-dialog {
    max-width: 80% !important;
    margin: 1.75rem auto !important;
  }
  
  /* Jika sidebar visible (body TIDAK punya class sidebar-collapse) */
  body:not(.sidebar-collapse) .modal-dialog {
    margin-left: calc(250px + 3.2rem) !important;
    margin-right: 2rem !important;
  }
  
  /* Jika sidebar hidden (body punya class sidebar-collapse) */
  body.sidebar-collapse .modal-dialog {
    margin-left: auto !important;
    margin-right: auto !important;
  }
  
  /* ===== BADGE STYLES ===== */
  .badge-pk {
    background-color: #007bff;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    margin-top: 2px;
  }
  
  .badge-fk {
    background-color: #28a745;
    color: white;
    font-size: 0.7rem;
    padding: 2px 6px;
    margin-top: 2px;
  }
  
  /* ===== INVALID FEEDBACK DALAM INPUT-GROUP (Bootstrap 4 fix) ===== */
  /* Bootstrap 4: invalid-feedback tidak auto-tampil jika input ada dalam input-group */
  /* JS sudah handle dengan .css('display','block'), ini sebagai backup */
  #wmu_akses_tabel_error:not(:empty) {
    display: block !important;
    color: #dc3545;
    font-size: 80%;
    width: 100%;
    margin-top: 0.25rem;
  }

  /* ===== DISABLED INPUTS STYLING (PINK/MERAH MUDA) ===== */
  input:disabled, select:disabled, textarea:disabled {
    background-color: #ffe6f0 !important;
    cursor: not-allowed !important;
    opacity: 0.8;
  }
  
  .custom-control-input:disabled ~ .custom-control-label {
    color: #ff69b4 !important;
    cursor: not-allowed !important;
  }
  
  .custom-control-input:disabled ~ .custom-control-label::before {
    background-color: #ffe6f0 !important;
    border-color: #ff69b4 !important;
  }
  
  /* Checkbox disabled CHECKED - Checkmark biru terang */
  .custom-control-input:disabled:checked ~ .custom-control-label::before {
    background-color: #007bff !important;
    border-color: #007bff !important;
    opacity: 1 !important;
  }
  
  .custom-control-input:disabled:checked ~ .custom-control-label::after {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%23fff' d='M6.564.75l-3.59 3.612-1.538-1.55L0 4.26l2.974 2.99L8 2.193z'/%3e%3c/svg%3e") !important;
    opacity: 1 !important;
  }
  
  /* ===== FK DISPLAY COLUMNS SELECTOR ===== */
  .fk-display-cols {
    margin-top: 5px;
    padding: 5px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background-color: #f8f9fa;
  }
  
  .fk-display-cols label {
    display: block;
    margin-bottom: 3px;
    font-size: 0.875rem;
  }
  
  /* ===== FIELD CONFIG TABLE (khusus update) ===== */
  .field-config-table {
    font-size: 0.85rem;
  }
  
  .field-config-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    white-space: nowrap;
  }
  
  .field-config-table td {
    vertical-align: top !important;
  }
  
  .field-config-table input,
  .field-config-table select {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
  }
  
  .field-config-table .form-control-sm {
    height: calc(1.5em + 0.5rem);
  }
  
  .field-config-table .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
  }
  
  /* ===== SPECIAL STYLING ===== */
  /* Kategori Menu disabled (tetap abu-abu karena readonly field) */
  #wmu_kategori_menu:disabled {
    background-color: #e9ecef !important;
    color: #6c757d !important;
  }
  
  /* Max input disabled */
  .max-length-input:disabled {
    background-color: #ffe6f0 !important;
  }
  
  /* Tooltip untuk disabled elements */
  input:disabled:hover, 
  select:disabled:hover, 
  textarea:disabled:hover,
  .custom-control-input:disabled ~ .custom-control-label:hover {
    cursor: not-allowed !important;
  }
</style>
