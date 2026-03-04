{{--
    Component: Menu Master Styles
    CSS global untuk semua template menu master (create, update, detail, delete, index)
    
    Diload via: @include('sisfo::components.menu-master.style')
--}}
<style>
/* ==========================================================
   MEDIA UPLOAD STYLES
   ========================================================== */
.media-upload-wrapper {
    position: relative;
}
.custom-file-upload-area {
    position: relative;
    border: 2px dashed #cbd5e0;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #fafbfc;
}
.custom-file-upload-area:hover {
    border-color: #4299e1;
    background: #ebf8ff;
}
.custom-file-upload-area.drag-over {
    border-color: #48bb78;
    background: #f0fff4;
}
.custom-file-upload-area .media-upload-input {
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 2;
}
.upload-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    pointer-events: none;
}
.upload-icon i {
    font-size: 28px;
    opacity: 0.6;
}
.upload-main-text {
    font-size: 13px;
    font-weight: 600;
    color: #4a5568;
    display: block;
}
.upload-sub-text {
    font-size: 11px;
    color: #a0aec0;
    display: block;
}

/* Preview Card */
.media-preview-container {
    margin-top: 8px;
}
.preview-card {
    display: flex;
    align-items: center;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: #fff;
    gap: 12px;
    margin-top: 8px;
    transition: box-shadow 0.15s;
}
.preview-card:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.preview-card-thumb {
    flex-shrink: 0;
    width: 64px;
    height: 64px;
    border-radius: 6px;
    overflow: hidden;
    border: 1px solid #e2e8f0;
    background: #f7fafc;
}
.preview-card-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.preview-card-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    border-radius: 8px;
    background: #f7fafc;
    display: flex;
    align-items: center;
    justify-content: center;
}
.preview-card-info {
    flex: 1;
    min-width: 0;
}
.preview-card-name {
    font-size: 13px;
    font-weight: 600;
    color: #2d3748;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.preview-card-meta {
    font-size: 11px;
    color: #a0aec0;
    margin-top: 2px;
}
.preview-card-actions {
    flex-shrink: 0;
    display: flex;
    gap: 4px;
}
.preview-card .btn-remove-file {
    color: #e53e3e;
    background: none;
    border: 1px solid #fed7d7;
    border-radius: 6px;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.15s;
    padding: 0;
}
.preview-card .btn-remove-file:hover {
    background: #fff5f5;
    border-color: #fc8181;
}

/* ==========================================================
   FK MODAL STYLES
   ========================================================== */

/* ── Modal Container ─────────────────────────────────────── */
.fk-modal-content {
    border: none;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18), 0 4px 16px rgba(0,0,0,0.1);
}

/* ── Header ──────────────────────────────────────────────── */
.fk-modal-header {
    background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
    padding: 20px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.fk-modal-header-inner {
    display: flex;
    align-items: center;
    gap: 14px;
}
.fk-modal-icon {
    width: 42px;
    height: 42px;
    background: rgba(255,255,255,0.18);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 17px;
    color: #fff;
    flex-shrink: 0;
}
.fk-modal-title {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #fff;
    line-height: 1.2;
}
.fk-modal-subtitle {
    margin: 0;
    font-size: 12px;
    color: rgba(255,255,255,0.7);
    line-height: 1.2;
}
.fk-modal-close {
    width: 34px;
    height: 34px;
    background: rgba(255,255,255,0.15);
    border: 1.5px solid rgba(255,255,255,0.25);
    border-radius: 8px;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    flex-shrink: 0;
}
.fk-modal-close:hover {
    background: rgba(255,255,255,0.28);
    border-color: rgba(255,255,255,0.45);
}

/* ── Body ────────────────────────────────────────────────── */
.fk-modal-body {
    padding: 24px;
    background: #f8faff;
}

/* ── Search Bar ──────────────────────────────────────────── */
.fk-search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
    margin-bottom: 16px;
}
.fk-search-wrapper:focus-within {
    border-color: #1a73e8;
    box-shadow: 0 0 0 4px rgba(26,115,232,0.1);
}
.fk-search-icon {
    padding: 0 14px;
    color: #94a3b8;
    font-size: 15px;
    flex-shrink: 0;
}
.fk-search-input {
    flex: 1;
    border: none;
    outline: none;
    padding: 12px 0;
    font-size: 14px;
    color: #1e293b;
    background: transparent;
}
.fk-search-input::placeholder { color: #b0bec5; }
.fk-search-clear {
    background: none;
    border: none;
    padding: 0 14px;
    color: #94a3b8;
    font-size: 16px;
    cursor: pointer;
    transition: color 0.2s;
    flex-shrink: 0;
}
.fk-search-clear:hover { color: #ef4444; }

/* ── Info Bar ────────────────────────────────────────────── */
.fk-info-bar {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}
.fk-info-badge {
    display: inline-flex;
    align-items: center;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: #e2e8f0;
    padding: 4px 10px;
    border-radius: 20px;
}

/* ── Table ───────────────────────────────────────────────── */
.fk-table-wrapper {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    max-height: 320px;
    overflow-y: auto;
}
.fk-table-wrapper::-webkit-scrollbar { width: 6px; }
.fk-table-wrapper::-webkit-scrollbar-track { background: #f1f5f9; }
.fk-table-wrapper::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
.fk-table-wrapper::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

.fk-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 13.5px;
}
.fk-table thead {
    position: sticky;
    top: 0;
    z-index: 2;
}
.fk-table thead tr {
    background: linear-gradient(180deg, #f0f4ff 0%, #e8eeff 100%);
}
.fk-table thead th {
    padding: 11px 14px;
    font-weight: 700;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    border-bottom: 2px solid #dde3f5;
    white-space: nowrap;
}
.fk-table thead th:first-child { text-align: center; width: 48px; }
.fk-table thead th:last-child  { text-align: center; width: 80px; }

.fk-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
    cursor: pointer;
}
.fk-table tbody tr:last-child { border-bottom: none; }
.fk-table tbody tr:hover { background: #eef4ff; }
.fk-table tbody tr:hover .fk-btn-select { opacity: 1; transform: scale(1.05); }

.fk-table tbody td {
    padding: 10px 14px;
    color: #334155;
    vertical-align: middle;
}
.fk-table tbody td:first-child {
    text-align: center;
    color: #94a3b8;
    font-size: 12px;
    font-weight: 600;
    width: 48px;
}
.fk-table tbody td:last-child { text-align: center; }

/* ── Select Button ───────────────────────────────────────── */
.fk-btn-select {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    padding: 6px 14px;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: #fff;
    border: none;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 2px 6px rgba(34,197,94,0.35);
    white-space: nowrap;
    opacity: 0.85;
}
.fk-btn-select:hover {
    background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);
    box-shadow: 0 4px 12px rgba(34,197,94,0.45);
    transform: scale(1.05);
    opacity: 1;
}
.fk-btn-select i { font-size: 11px; }

/* ── Empty State ─────────────────────────────────────────── */
.fk-empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #94a3b8;
}
.fk-empty-state i { font-size: 36px; margin-bottom: 10px; opacity: 0.4; display: block; }
.fk-empty-state p { margin: 0; font-size: 14px; }

/* ── Pagination Bar ──────────────────────────────────────── */
.fk-pagination-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #e2e8f0;
}
.fk-page-info {
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
}
.fk-pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
}
.fk-pagination li a,
.fk-pagination li span {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    color: #475569;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.18s;
    line-height: 1;
}
.fk-pagination li a:hover {
    background: #eef4ff;
    border-color: #1a73e8;
    color: #1a73e8;
}
.fk-pagination li.active a {
    background: linear-gradient(135deg, #1a73e8 0%, #1558b0 100%);
    border-color: #1a73e8;
    color: #fff;
    box-shadow: 0 2px 8px rgba(26,115,232,0.35);
}
.fk-pagination li.disabled span,
.fk-pagination li.disabled a {
    color: #cbd5e1;
    border-color: #f1f5f9;
    background: #f8fafc;
    cursor: not-allowed;
    pointer-events: none;
}
.fk-pagination li.fk-pg-arrow a { font-size: 11px; }

/* ==========================================================
   FK SEARCH ROW HOVER
   ========================================================== */
.fk-row-selectable:hover,
.fk-row-selectable-edit:hover {
    background-color: #ebf8ff !important;
}

/* ==========================================================
   TIME / DATE INPUT WRAPPER (single & range)
   ========================================================== */
/* Input group dengan ikon di sebelah kanan */
.time-input-group {
    flex-wrap: nowrap;
}
.time-input-group .time-input-field {
    cursor: pointer;
    border-right: none;         /* hilangkan border kanan agar menyatu dengan addon */
    border-radius: 4px 0 0 4px;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.time-input-group .time-input-field:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
    z-index: 3;
}
/* Addon ikon di kanan — pointer agar terasa klikable */
.time-icon-addon {
    background: #f8f9fa;
    border-left: none;
    border-radius: 0 4px 4px 0;
    color: #6c757d;
    cursor: pointer;
    padding: 0 10px;
    font-size: 14px;
    transition: background 0.15s, color 0.15s;
    user-select: none;
}
.time-input-group:focus-within .time-icon-addon {
    border-color: #80bdff;
    background: #e8f4ff;
    color: #0069d9;
}

/* Range container — susun "Dari" dan "s/d" secara vertikal dengan pemisah */
.range-input-container {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.range-input-row {
    display: flex;
    align-items: center;
    gap: 8px;
}
.range-input-label-col {
    flex: 0 0 36px;
    text-align: right;
}
.range-label {
    font-size: 12px;
    font-weight: 600;
    color: #495057;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px 6px;
    white-space: nowrap;
}
.range-input-col {
    flex: 1;
}
/* Pemisah panah bawah antar baris range */
.range-input-divider {
    text-align: center;
    padding: 0 0 0 44px;
    font-size: 11px;
    color: #adb5bd;
    line-height: 1;
}
</style>
