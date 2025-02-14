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
        <div class="card shadow">
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
                        <i class="fa fa-upload fa-2x"></i>
                        <p>Drag and drop or <a href="#">browse</a> to upload</p>
                        <p class="text-muted">PNG, JPG, GIF up to 10MB</p>
                        <input type="file" class="d-none" id="uploadFile" accept="image/png, image/jpeg, image/gif">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning w-100">DAFTAR</button>
            </form>
        </div>
    </div>
</body>
</html>