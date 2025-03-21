<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar informasi</title>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js"></script>
    <link rel="stylesheet" href="styles.css">
    @vite(['resources/css/app.css', 'resources/js/previewPDF.js'])
    <script>
        window.pdfUrl = "{{asset('storage/pdf/tryinformasi.pdf') }}";
    </script>
</head>
<body>
    <div class="container" x-data="pdfViewer">
        <h2 class="title">Informasi Dikecualikan</h2>
        <div class="pdf-container">
            <canvas id="pdf-canvas"></canvas>
        </div>
        <div class="controls">
            <button @click="prevPage" :disabled="page <= 1">❮</button>
            <span x-text="page"></span> / <span x-text="totalPages"></span>
            <button @click="nextPage" :disabled="page >= totalPages">❯</button>
        </div>
    </div>
</body>
</html>
