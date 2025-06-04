<!-- filepath: c:\laragon\www\PPID-polinema\Modules\Sisfo\resources\views\Email\verif-pengajuan.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Permohonan Informasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .email-container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .email-header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .email-header p {
            margin: 10px 0 0 0;
            opacity: 0.9;
        }
        .email-body {
            padding: 40px 30px;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .info-card {
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: 600;
            width: 120px;
            color: #495057;
        }
        .info-value {
            flex: 1;
            color: #212529;
        }
        .message-box {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .message-box p {
            margin: 0;
            line-height: 1.6;
            color: #1565c0;
        }
        .email-footer {
            background-color: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #dee2e6;
        }
        .footer-text {
            color: #6c757d;
            font-size: 14px;
            margin: 0;
        }
        .logo {
            width: 50px;
            height: 50px;
            margin-bottom: 15px;
        }
        @media (max-width: 600px) {
            .email-container {
                margin: 20px;
                border-radius: 5px;
            }
            .email-body {
                padding: 20px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                width: auto;
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            <div class="logo-container">
                <!-- Logo bisa ditambahkan di sini -->
                <svg class="logo" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7L12 12L22 7L12 2Z"></path>
                    <polyline points="2,17 12,22 22,17"></polyline>
                    <polyline points="2,12 12,17 22,12"></polyline>
                </svg>
            </div>
            <h1>UPA TIK Polinema</h1>
            <p>Sistem Informasi PPID</p>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">
                <h3>Halo, {{ $nama }}</h3>
                <p>Kami ingin menginformasikan status terbaru dari permohonan informasi yang Anda ajukan.</p>
            </div>

            <!-- Status Badge -->
            <div class="text-center">
                <span class="status-badge {{ $status === 'Disetujui' ? 'status-approved' : 'status-rejected' }}">
                    {{ $status }}
                </span>
            </div>

            <!-- Informasi Detail -->
            <div class="info-card">
                <h5 class="mb-3">Detail Permohonan</h5>
                <div class="info-row">
                    <div class="info-label">Nama:</div>
                    <div class="info-value">{{ $nama }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Kategori:</div>
                    <div class="info-value">{{ $kategori }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status Pemohon:</div>
                    <div class="info-value">{{ $statusPemohon }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Informasi:</div>
                    <div class="info-value">{{ $informasiYangDibutuhkan }}</div>
                </div>
                <div class="info-row">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <strong class="{{ $status === 'Disetujui' ? 'text-success' : 'text-danger' }}">
                            {{ $status }}
                        </strong>
                    </div>
                </div>
                @if($status === 'Ditolak' && $reason)
                <div class="info-row">
                    <div class="info-label">Alasan:</div>
                    <div class="info-value text-danger">{{ $reason }}</div>
                </div>
                @endif
            </div>

            <!-- Pesan Verifikasi -->
            <div class="message-box">
                <h6 class="mb-2">
                    @if($status === 'Disetujui')
                        <i class="fas fa-check-circle text-success"></i> Informasi Selanjutnya
                    @else
                        <i class="fas fa-exclamation-circle text-warning"></i> Informasi Penting
                    @endif
                </h6>
                <p>{{ $pesanVerifikasi }}</p>
            </div>

            @if($status === 'Disetujui')
            <div class="alert alert-info">
                <h6 class="alert-heading">Langkah Selanjutnya:</h6>
                <p class="mb-0">Tim kami akan melakukan review lebih lanjut terhadap permohonan Anda. Kami akan menghubungi Anda kembali jika diperlukan informasi tambahan atau ketika proses review telah selesai.</p>
            </div>
            @else
            <div class="alert alert-warning">
                <h6 class="alert-heading">Anda dapat mengajukan kembali:</h6>
                <p class="mb-0">Jika Anda merasa keberatan dengan keputusan ini, Anda dapat mengajukan permohonan baru dengan melengkapi persyaratan yang diperlukan melalui sistem kami.</p>
            </div>
            @endif

            <div class="contact-info mt-4">
                <h6>Butuh Bantuan?</h6>
                <p class="mb-1">Jika Anda memiliki pertanyaan atau memerlukan bantuan lebih lanjut, silakan hubungi kami:</p>
                <ul class="list-unstyled">
                    <li><strong>Email:</strong> ppid@polinema.ac.id</li>
                    <li><strong>Telepon:</strong> (0341) 404424</li>
                    <li><strong>Website:</strong> ppid.polinema.ac.id</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">
                <strong>UPA TIK Politeknik Negeri Malang</strong><br>
                Jl. Soekarno Hatta No.9, Malang, Jawa Timur 65141<br>
                Email otomatis ini dikirim dari sistem PPID Polinema
            </p>
            <hr class="my-3">
            <p class="footer-text">
                <small>© {{ date('Y') }} Politeknik Negeri Malang. Seluruh hak cipta dilindungi.</small>
            </p>
        </div>
    </div>
</body>
</html>