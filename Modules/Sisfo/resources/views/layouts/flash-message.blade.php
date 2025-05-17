<!-- Simpan sebagai resources/views/sisfo/layouts/flash-message.blade.php -->
@if (session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('warning'))
<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle mr-2"></i> {{ session('warning') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('info'))
<div class="alert alert-info alert-dismissible fade show" role="alert">
    <i class="fas fa-info-circle mr-2"></i> {{ session('info') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<script>
    // Pastikan script hanya dijalankan setelah jQuery dimuat
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Periksa apakah jQuery tersedia
            if (typeof jQuery !== 'undefined') {
                // Gunakan pendekatan yang lebih aman untuk auto-hide
                setTimeout(function() {
                    // Sembunyikan alert perlahan menggunakan jQuery
                    jQuery(".alert").fadeOut(500, function() {
                        jQuery(this).remove(); // Hapus dari DOM setelah fadeout
                    });
                }, 5000);
                
                console.log('Pesan flash akan otomatis hilang setelah 5 detik');
            } else {
                // Fallback menggunakan JavaScript murni jika jQuery tidak tersedia
                setTimeout(function() {
                    var alerts = document.querySelectorAll('.alert');
                    alerts.forEach(function(alert) {
                        alert.style.transition = 'opacity 0.5s';
                        alert.style.opacity = '0';
                        setTimeout(function() {
                            if (alert.parentNode) {
                                alert.parentNode.removeChild(alert);
                            }
                        }, 500);
                    });
                }, 5000);
                
                console.log('Pesan flash akan otomatis hilang menggunakan vanilla JS');
            }
        } catch (error) {
            console.error('Terjadi kesalahan pada auto-hide pesan flash:', error);
        }
    });
</script>