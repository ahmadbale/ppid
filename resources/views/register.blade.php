<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register PPID</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="container py-5">
        <div class="text-center mb-4">
            <img src="{{ asset('img/PPIDlogo.svg') }}" alt="PPID Logo" class="logo">
        </div>
        <div class="card-register shadow">
            <h3 class="text-center mb-4">Daftar Akun Baru</h3>
            <form>
                <div class="mb-3">
                    <label class="form-label">Nama Lengkap (sesuai KTP) *</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail *</label>
                    <input type="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nomor HP *</label>
                    <input type="text" class="form-control" required>
                </div>
                  <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Password *</label>
                        <div class="position-relative">
                            <input type="password" id="password" class="form-control" required>
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('password')"></i>
                        </div>
                    </div>
                    <div class="col">
                        <label class="form-label">Confirm Password *</label>
                        <div class="position-relative">
                            <input type="password" id="confirmPassword" class="form-control" required>
                            <i class="fa fa-eye password-toggle" onclick="togglePassword('confirmPassword')"></i>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat *</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pekerjaan *</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIK *</label>
                    <input type="text" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Upload Foto KTP *</label>
                    <div class="upload-box">
                    <div class="upload-zone relative border-2 border-dashed border-gray-300 rounded-lg p-6 transition-all hover:border-orange-500 text-center">
                        <input type="file" id="ktp-upload" class="hidden" style="display: none;" accept="image/*">
                        <div id="upload-content">
                            <img src="" alt="" id="preview-image" class="max-h-32 mx-auto mb-4 hidden">
                            <div class="upload-placeholder">
                                <i class="fas fa-upload text-4xl text-gray-400 mb-3"></i>
                                <p class="text-sm text-gray-600">
                                    Drag and drop <span class="text-orange-500 font-semibold">or choose file</span> to upload
                                </p>
                                <p class="text-xs text-gray-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                                <button id="upload-btn" class="btn btn-warning  px-4 py-2 shadow-sm">
                                    <i class="fas fa-upload"></i> Pilih File
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="upload-progress hidden">
                        <div class="w-full bg-gray-200 rounded-full h-2.5">
                            <div class="bg-orange-500 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2 text-center">Mengupload... <span class="progress-text">0%</span></p>
                    </div>
                </div>
            </div>
                <button type="submit" class="btn btn-warning w-100">DAFTAR</button>
            </form>
        </div>
    </div>
</body>
</html>