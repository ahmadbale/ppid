<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Review Pernyataan Keberatan - PPID Polinema</title>
    <!-- Bootstrap 4 dari template -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome dari template -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Google Fonts untuk konsistensi -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            line-height: 1.6;
            color: #212529;
            padding: 20px 0;
        }
        
        .email-wrapper {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }
        
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .email-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            right: 0;
            height: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%);
        }
        
        .logo-container {
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .logo-container i {
            font-size: 36px;
            color: white;
        }
        
        .header-title {
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            font-weight: 300;
        }
        
        .email-content {
            padding: 40px 30px;
            background: linear-gradient(135deg, #f8f9ff 0%, #f3e7f0 100%);
        }
        
        .greeting {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .greeting h4 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .greeting p {
            color: #6c757d;
            font-size: 16px;
            margin-bottom: 0;
        }
        
        .status-container {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 15px;
        }
        
        .status-approved {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }
        
        .status-rejected {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            color: white;
        }
        
        .info-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #667eea;
        }
        
        .info-card h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .info-item {
            display: flex;
            padding: 8px 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6c757d;
            width: 140px;
            flex-shrink: 0;
        }
        
        .info-value {
            color: #495057;
            flex: 1;
        }
        
        .jawaban-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #28a745;
        }
        
        .jawaban-section h6 {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .jawaban-section p {
            color: #495057;
            line-height: 1.7;
            margin-bottom: 0;
        }
        
        .file-attachment {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            text-align: center;
        }
        
        .file-attachment i {
            font-size: 24px;
            color: #dc3545;
            margin-bottom: 10px;
        }
        
        .file-attachment p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
        
        .message-box {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-left: 4px solid #dc3545;
        }
        
        .message-box h6 {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .message-box p {
            color: #495057;
            line-height: 1.7;
            margin-bottom: 0;
        }
        
        .contact-section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        
        .contact-section h6 {
            color: #495057;
            font-weight: 600;
            margin-bottom: 20px;
        }
        
        .contact-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .contact-list li {
            padding: 8px 0;
            color: #6c757d;
            position: relative;
            padding-left: 25px;
        }
        
        .contact-list li::before {
            content: '•';
            color: #667eea;
            font-weight: bold;
            position: absolute;
            left: 8px;
        }
        
        .contact-list strong {
            color: #495057;
        }
        
        .email-footer {
            background: #343a40;
            color: #adb5bd;
            padding: 25px 30px;
            text-align: center;
        }
        
        .footer-content {
            margin-bottom: 15px;
        }
        
        .footer-content strong {
            color: white;
            display: block;
            margin-bottom: 5px;
        }
        
        .copyright {
            font-size: 14px;
            opacity: 0.8;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            .email-wrapper {
                margin: 10px;
                border-radius: 12px;
            }
            
            .email-header,
            .email-content {
                padding: 25px 20px;
            }
            
            .header-title {
                font-size: 22px;
            }
            
            .info-item {
                flex-direction: column;
            }
            
            .info-label {
                width: 100%;
                margin-bottom: 5px;
                font-weight: 600;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header Section -->
        <div class="email-header">
            <div class="logo-container">
                <i class="fas fa-university"></i>
            </div>
            <h2 class="header-title">PPID POLINEMA</h2>
            <p class="header-subtitle">Hasil Review Pernyataan Keberatan</p>
        </div>

        <!-- Content Section -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <h4>Halo, {{ $nama }}</h4>
                <p>{{ $pesanReview }}</p>
            </div>

            <!-- Status Badge -->
            <div class="status-container">
                <span class="status-badge {{ $status === 'Disetujui' ? 'status-approved' : 'status-rejected' }}">
                    @if($status === 'Disetujui')
                        <i class="fas fa-check-circle"></i> PERNYATAAN KEBERATAN DISETUJUI
                    @else
                        <i class="fas fa-times-circle"></i> PERNYATAAN KEBERATAN DITOLAK
                    @endif
                </span>
            </div>

            <!-- Detail Pernyataan Keberatan -->
            <div class="info-card">
                <h6><i class="fas fa-file-alt"></i> Detail Pernyataan Keberatan</h6>
                <div class="info-item">
                    <div class="info-label">Kategori:</div>
                    <div class="info-value">{{ $kategori }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Alasan Pengajuan:</div>
                    <div class="info-value">{{ $alasanPengajuanKeberatan }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Kasus Posisi:</div>
                    <div class="info-value">{{ $kasusPosisi }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Status:</div>
                    <div class="info-value">
                        <strong class="{{ $status === 'Disetujui' ? 'text-success' : 'text-danger' }}">
                            {{ $status }}
                        </strong>
                    </div>
                </div>
            </div>

            @if($status === 'Disetujui' && $jawaban)
                <!-- Jawaban Section -->
                <div class="jawaban-section">
                    <h6><i class="fas fa-reply"></i> Jawaban/Tanggapan</h6>
                    <p>{{ $jawaban }}</p>
                    
                    @if(preg_match('/\.(pdf|doc|docx|jpg|jpeg|png|gif)$/i', $jawaban))
                        <div class="file-attachment">
                            <i class="fas fa-file-pdf"></i>
                            <p><strong>Dokumen terlampir dalam email ini</strong></p>
                            <p>Silakan buka lampiran untuk melihat dokumen lengkap</p>
                        </div>
                    @endif
                </div>
            @endif

            @if($status === 'Ditolak' && $reason)
                <!-- Alasan Penolakan -->
                <div class="message-box">
                    <h6><i class="fas fa-exclamation-triangle"></i> Alasan Penolakan</h6>
                    <p>{{ $reason }}</p>
                </div>
            @endif

            <!-- Contact Information -->
            <div class="contact-section">
                <h6><i class="fas fa-phone"></i> Butuh Bantuan?</h6>
                <ul class="contact-list">
                    <li><strong>Email:</strong> ppid@polinema.ac.id</li>
                    <li><strong>Telepon:</strong> 085804049240</li>
                    <li><strong>Website:</strong> ppid.polinema.ac.id</li>
                </ul>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <div class="footer-content">
                <strong>Politeknik Negeri Malang</strong>
                <span>Pejabat Pengelola Informasi dan Dokumentasi (PPID)</span>
            </div>
            <div class="copyright">
                © {{ date('Y') }} PPID Polinema. Pesan otomatis dari sistem.
            </div>
        </div>
    </div>
</body>
</html>