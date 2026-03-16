{{--
    Web Menu Global - Shared Styles Component
    Digunakan oleh: create.blade.php dan update.blade.php
    Load via: @include('sisfo::components.web-menu-global.style')
--}}
<style>
/* ==========================================================
   CUSTOM SEARCHABLE SELECT — Reusable Component
   ========================================================== */
.ss-wrapper {
    position: relative;
    font-family: inherit;
}

.ss-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding: 6px 12px;
    background: #fff;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    min-height: 38px;
    transition: border-color .15s, box-shadow .15s;
    user-select: none;
}

.ss-trigger.has-value {
    align-items: flex-start;
    padding-top: 8px;
    padding-bottom: 8px;
}

.ss-trigger:hover  { border-color: #adb5bd; }
.ss-trigger.open   { border-color: #80bdff; box-shadow: 0 0 0 .2rem rgba(0,123,255,.25); }
.ss-trigger.is-invalid { border-color: #dc3545; }
.ss-trigger.is-invalid.open { box-shadow: 0 0 0 .2rem rgba(220,53,69,.25); }

.ss-display {
    flex: 1;
    overflow: hidden;
    white-space: nowrap;
    text-overflow: ellipsis;
    font-size: .875rem;
}

.ss-display.placeholder { color: #6c757d; }

.ss-display.ss-display-selected {
    white-space: normal;
    overflow: visible;
    text-overflow: unset;
}

.ss-display-selected .ss-sel-app  { font-size: .7rem; color: #6c757d; line-height: 1.2; }
.ss-display-selected .ss-sel-name { font-size: .85rem; font-weight: 600; color: #343a40; line-height: 1.3; }
.ss-display-selected .ss-sel-desc { font-size: .72rem; color: #868e96; line-height: 1.2; }

.ss-actions {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.ss-trigger.has-value .ss-actions { margin-top: 2px; }

.ss-clear-btn {
    display: none;
    background: none;
    border: none;
    padding: 0 2px;
    color: #adb5bd;
    cursor: pointer;
    line-height: 1;
    font-size: .8rem;
    transition: color .15s;
}

.ss-clear-btn:hover { color: #dc3545; }

.ss-arrow {
    color: #6c757d;
    font-size: .75rem;
    transition: transform .2s;
}

.ss-trigger.open .ss-arrow { transform: rotate(180deg); }

/* Dropdown panel */
.ss-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 3px);
    left: 0;
    right: 0;
    z-index: 1055;
    background: #fff;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12), 0 2px 8px rgba(0,0,0,.08);
    overflow: hidden;
}

.ss-dropdown.open { display: block; }

/* Search bar */
.ss-search-wrap {
    padding: 8px;
    border-bottom: 1px solid #f0f0f0;
    background: #fafafa;
    position: sticky;
    top: 0;
    z-index: 1;
}

.ss-search-input {
    width: 100%;
    padding: 6px 10px 6px 30px;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    font-size: .85rem;
    outline: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236c757d' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.35-4.35'/%3E%3C/svg%3E") no-repeat 8px center;
}

.ss-search-input:focus { border-color: #80bdff; box-shadow: 0 0 0 2px rgba(0,123,255,.15); }
.ss-search-input::placeholder { color: #adb5bd; }

/* Stats */
.ss-stats {
    padding: 4px 10px;
    font-size: .72rem;
    color: #868e96;
    background: #fafafa;
    border-bottom: 1px solid #f5f5f5;
}

/* Options list */
.ss-options-list {
    max-height: 280px;
    overflow-y: auto;
    overscroll-behavior: contain;
}

.ss-options-list::-webkit-scrollbar { width: 5px; }
.ss-options-list::-webkit-scrollbar-track { background: #f8f9fa; }
.ss-options-list::-webkit-scrollbar-thumb { background: #ced4da; border-radius: 3px; }

/* Group header */
.ss-group-header {
    padding: 6px 12px 2px;
    font-size: .68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .06em;
    color: #868e96;
    background: #f8f9fa;
    border-top: 1px solid #f0f0f0;
    position: sticky;
    top: 0;
}

.ss-group-header:first-child { border-top: none; }

/* Option item */
.ss-option-item {
    padding: 8px 12px;
    cursor: pointer;
    transition: background .1s;
    border-bottom: 1px solid #f8f9fa;
}

.ss-option-item:hover,
.ss-option-item.focused  { background: #e8f4fd; }
.ss-option-item.selected { background: #d4edff; }

.ss-opt-name {
    font-size: .83rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.3;
}

.ss-opt-desc {
    font-size: .73rem;
    color: #6c757d;
    line-height: 1.3;
    margin-top: 1px;
}

.ss-opt-name mark,
.ss-opt-desc mark {
    background: #fff3cd;
    padding: 0 1px;
    border-radius: 2px;
    color: inherit;
}

/* Badge "Baru" */
.ss-badge-new {
    display: inline-block;
    font-size: .62rem;
    font-weight: 700;
    padding: 1px 5px;
    background: #28a745;
    color: #fff;
    border-radius: 10px;
    vertical-align: middle;
    margin-left: 4px;
    letter-spacing: .03em;
}

/* Empty state */
.ss-empty {
    padding: 24px 16px;
    text-align: center;
    color: #adb5bd;
    font-size: .83rem;
}

.ss-empty i {
    display: block;
    font-size: 1.5rem;
    margin-bottom: 8px;
    opacity: .5;
}
</style>
