@php
  use Modules\Sisfo\App\Models\Website\WebMenuModel;
  $getIpDinamisTabelInformasiSetiapSaatUrl = WebMenuModel::getDynamicMenuUrl('get-informasi-publik-informasi-setiap-saat');
  $downloadUrl = url($getIpDinamisTabelInformasiSetiapSaatUrl . '/detailData/' . $id . '?type=' . $type . '&action=download');
@endphp
<div class="modal-header">
    <h5 class="modal-title">
        <i class="fas fa-file-pdf text-danger mr-2"></i>
        {{ $title }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<div class="modal-body p-0">
    <div class="document-viewer">
        <div class="d-flex justify-content-between align-items-center p-3 bg-light border-bottom">
            <div class="document-info">
                <h6 class="mb-0">
                    <i class="fas fa-file-pdf text-danger mr-2"></i>{{ $filename }}.pdf
                </h6>
            </div>
            <div class="document-actions">
                <button onclick="openFullscreen()" class="btn btn-sm btn-info">
                    <i class="fas fa-expand-arrows-alt mr-1"></i> Fullscreen
                </button>
                <a href="{{ $downloadUrl }}" class="btn btn-sm btn-primary" target="_blank">
                    <i class="fas fa-download mr-1"></i> Download
                </a>
            </div>
        </div>
        
        <div class="pdf-container" style="height: 70vh; overflow: auto;">
            <iframe id="pdfViewer" 
                    src="{{ $documentUrl }}" 
                    width="100%" 
                    height="100%" 
                    style="border: none;"
                    type="application/pdf">
                <p>Browser Anda tidak mendukung tampilan PDF. 
                   <a href="{{ $downloadUrl }}" target="_blank">
                       Download PDF
                   </a>
                </p>
            </iframe>
        </div>
    </div>
</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">
        <i class="fas fa-times mr-1"></i>
        Tutup
    </button>
    <a href="{{ $downloadUrl }}" 
       class="btn btn-primary" target="_blank">
        <i class="fas fa-download mr-1"></i>
        Download PDF
    </a>
</div>

<script>
function openFullscreen() {
    const pdfUrl = '{{ $documentUrl }}';
    window.open(pdfUrl, '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
}

// Handle iframe load error
document.getElementById('pdfViewer').onerror = function() {
    this.style.display = 'none';
    this.parentNode.innerHTML = `
        <div class="text-center p-5">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h5>Tidak dapat menampilkan PDF</h5>
            <p class="text-muted">Browser Anda tidak mendukung preview PDF.</p>
            <a href="{{ $downloadUrl }}" class="btn btn-primary" target="_blank">
                <i class="fas fa-download mr-1"></i>
                Download PDF
            </a>
        </div>
    `;
};
</script>

<style>
.document-viewer {
    background: #f8f9fa;
}

.pdf-container {
    background: white;
}

.modal-lg {
    max-width: 90% !important;
}

@media (max-width: 768px) {
    .modal-lg {
        max-width: 95% !important;
        margin: 10px;
    }
    
    .document-actions {
        flex-direction: column;
        gap: 5px;
    }
    
    .document-actions .btn {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }
}
</style>
