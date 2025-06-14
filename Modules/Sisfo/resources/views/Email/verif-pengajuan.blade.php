<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Permohonan Informasi - PPID Polinema</title>
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
            background: linear-gradient(135deg, #87CEEB 0%, #87CEFA 50%, #B0E0E6 100%);
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
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
            background: linear-gradient(135deg, #f0f8ff 0%, #e6f3ff 100%);
        }
        
        .greeting {
            margin-bottom: 30px;
        }
        
        .greeting h4 {
            color: #1f2937;
            font-weight: 500;
            margin-bottom: 10px;
        }
        
        .greeting p {
            color: #4b5563;
            font-size: 15px;
        }
        
        .status-container {
            text-align: center;
            margin: 30px 0;
        }
        
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .status-approved {
            background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
            color: #065f46;
            border: 2px solid #10b981;
        }
        
        .status-rejected {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            color: #7f1d1d;
            border: 2px solid #ef4444;
        }
        
        .info-card {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 15px;
            padding: 30px;
            margin: 25px 0;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        
        .info-card h6 {
            color: #374151;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 16px;
        }
        
        .info-item {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid rgba(241, 245, 249, 0.8);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #64748b;
            width: 140px;
            flex-shrink: 0;
            font-size: 14px;
        }
        
        .info-value {
            color: #1e293b;
            font-weight: 400;
            font-size: 14px;
        }
        
        .message-box {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
            border-left: 5px solid #3b82f6;
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.1);
        }
        
        .message-box h6 {
            color: #1e40af;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
        }
        
        .message-box p {
            color: #1e40af;
            margin: 0;
            font-size: 14px;
            line-height: 1.7;
        }
        
        .contact-section {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 12px;
            padding: 25px;
            margin: 25px 0;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
        }
        
        .contact-section h6 {
            color: #374151;
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 16px;
        }
        
        .contact-list {
            list-style: none;
            padding: 0;
        }
        
        .contact-list li {
            padding: 8px 0;
            color: #4b5563;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .contact-list li::before {
            content: '•';
            color: #3b82f6;
            font-weight: bold;
            font-size: 16px;
        }
        
        .contact-list strong {
            color: #1f2937;
        }
        
        .email-footer {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e2e8f0;
        }
        
        .footer-content {
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }
        
        .footer-content strong {
            color: #374151;
        }
        
        .copyright {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            color: #94a3b8;
            font-size: 12px;
        }
        
        /* Responsive Design */
        @media (max-width: 600px) {
            body {
                padding: 10px 0;
            }
            
            .email-wrapper {
                margin: 10px;
                border-radius: 12px;
            }
            
            .email-header {
                padding: 30px 20px;
            }
            
            .email-content {
                padding: 30px 20px;
            }
            
            .info-item {
                flex-direction: column;
                gap: 5px;
            }
            
            .info-label {
                width: auto;
                font-weight: 600;
            }
            
            .logo-container {
                width: 70px;
                height: 70px;
            }
            
            .logo-container i {
                font-size: 30px;
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
            <h1 class="header-title">PPID Polinema</h1>
            <p class="header-subtitle">Sistem Informasi Layanan Publik</p>
        </div>

        <!-- Content Section -->
        <div class="email-content">
            <!-- Greeting -->
            <div class="greeting">
                <h4>Halo, {{ $nama }}!</h4>
                <p>Berikut adalah update status terbaru untuk permohonan informasi Anda.</p>
            </div>

            <!-- Status Badge -->
            <div class="status-container">
                <div class="status-badge {{ $status === 'Disetujui' ? 'status-approved' : 'status-rejected' }}">
                    <i class="fas {{ $status === 'Disetujui' ? 'fa-check-circle' : 'fa-exclamation-triangle' }}"></i>
                    {{ $status }}
                </div>
            </div>

            <!-- Detail Information -->
            <div class="info-card">
                <h6><i class="fas fa-info-circle mr-2"></i>Detail Permohonan</h6>
                
                <div class="info-item">
                    <div class="info-label">Nama Pemohon</div>
                    <div class="info-value">{{ $nama }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Kategori</div>
                    <div class="info-value">{{ $kategori }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Status Pemohon</div>
                    <div class="info-value">{{ $statusPemohon }}</div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">Informasi Diminta</div>
                    <div class="info-value">{{ $informasiYangDibutuhkan }}</div>
                </div>
                
                @if($status === 'Ditolak' && $reason)
                <div class="info-item">
                    <div class="info-label">Alasan Penolakan</div>
                    <div class="info-value text-danger">{{ $reason }}</div>
                </div>
                @endif
            </div>

            <!-- Message Box -->
            <div class="message-box">
                <h6>
                    <i class="fas {{ $status === 'Disetujui' ? 'fa-info-circle' : 'fa-exclamation-circle' }}"></i>
                    Informasi Penting
                </h6>
                <p>{{ $pesanVerifikasi }}</p>
            </div>

            <!-- Contact Information -->
            <div class="contact-section">
                <h6><i class="fas fa-headset mr-2"></i>Butuh Bantuan?</h6>
                <p class="mb-3">Hubungi kami jika memerlukan bantuan lebih lanjut:</p>
                <ul class="contact-list">
                    <li><strong>Email:</strong> magangupatik@polinema.ac.id</li>
                    <li><strong>Telepon:</strong> 0858040492410</li>
                    <li><strong>Website:</strong> ppid.polinema.ac.id</li>
                </ul>
            </div>
        </div>

        <!-- Footer Section -->
        <div class="email-footer">
            <div class="footer-content">
                <strong>Politeknik Negeri Malang</strong><br>
                Jl. Soekarno Hatta No.9, Kota Malang, Jawa Timur 65141<br>
                <em>Email ini dikirim secara otomatis dari Sistem PPID</em>
            </div>
            <div class="copyright">
                © {{ date('Y') }} Politeknik Negeri Malang. Hak cipta dilindungi undang-undang.
            </div>
        </div>
    </div>
</body>
</html>