<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Permohonan Informasi Publik</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="background">
        <div class="circle large"></div>
        <div class="circle medium"></div>
        <div class="circle small"></div>
        <div class="dashed-circle"></div>
    </div>

    <div class="form-container">
        <h2>Form Permohonan Informasi</h2>
        <form action="submit.php" method="POST" enctype="multipart/form-data">
            <label for="nama">Nama Pemohon:</label>
            <input type="text" id="nama" name="nama" required>

            <label for="alamat">Alamat Pemohon:</label>
            <input type="text" id="alamat" name="alamat" required>

            <label for="pekerjaan">Pekerjaan Pemohon:</label>
            <input type="text" id="pekerjaan" name="pekerjaan" required>

            <label for="telepon">Nomor Telepon/HP:</label>
            <input type="tel" id="telepon" name="telepon" required>

            <label for="ktp">Upload Identitas (KTP/SIM/Paspor):</label>
            <input type="file" id="ktp" name="ktp" accept="image/*" required>

            <label for="keberatan">Pengajuan Keberatan Dilakukan Atas:</label>
            <select id="keberatan" name="keberatan">
                <option value="diri_sendiri">Diri Sendiri</option>
                <option value="orang_lain">Orang Lain</option>
            </select>

            <button type="submit">Kirim Permohonan</button>
        </form>
    </div>
</body>
</html>

<style>
/* body {
    margin: 0;
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
     
} */

.background {
    background-color: #1e1e5c;
    position: fixed;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: -1;
}

.circle {
    position: absolute;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
}

.large {
    width: 300px;
    height: 300px;
    left: 10%;
    top: 30%;
}

.medium {
    width: 200px;
    height: 200px;
    left: 40%;
    top: 20%;
}

.small {
    width: 100px;
    height: 100px;
    left: 70%;
    top: 60%;
}

.dashed-circle {
    position: absolute;
    width: 500px;
    height: 500px;
    border: 2px dashed white;
    border-radius: 50%;
    top: 0%;
    left: 0%;
    animation: rotate 10s infinite linear;
}

@keyframes rotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.form-container {
    background: rgba(255, 255, 255, 0.9);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    text-align: center;
    max-width: 400px;
    width: 90%;
}

h2 {
    color: #1e1e5c;
}

label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}

input, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border: 1px solid #ccc;
    border-radius: 5px;
}

button {
    background-color: #1e1e5c;
    color: white;
    padding: 10px;
    border: none;
    border-radius: 5px;
    margin-top: 15px;
    cursor: pointer;
}

button:hover {
    background-color: #0f1b3c;
}
</style>
