<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PPID Polinema</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="py-4" style="background: linear-gradient(to right, #1C4F99, #0F2E5A);">
        <div class="container d-flex justify-content-between align-items-center px-4">
            <div class="d-flex align-items-center gap-3">
                <img src="img/logo-polinema.png" alt="Logo Polinema" class="img-fluid" style="width: 80px;">
                <div>
                    <h1 class="text-white fs-5 fw-bold mb-0">PEJABAT PENGELOLA INFORMASI DAN DOKUMENTASI</h1>
                    <span class="text-warning fs-4 fw-bold">POLINEMA</span>
                </div>
            </div>
            <div class="position-relative d-flex align-items-center bg-white shadow-sm" style="border-radius: 30px; overflow: hidden; width: 350px;">
                <input type="text" class="form-control border-0 ps-3" placeholder="Cari di PPID Polinema..." style="border-radius: 0;">
                <button class="btn btn-warning px-3 d-flex align-items-center justify-content-center" style="border-radius: 0 30px 30px 0;">
                    <i class="bi bi-search text-white"></i>
                </button>
            </div>
        </div>
    </header>
</body>
</html>
