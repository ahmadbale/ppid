# Ide
Membuat Menu Tanpa Ngoding

# Tujuan
Membuat Sebuah Mekanisme untuk pembuatan menu Master tanpa perlu ngoding, dengan menggunakan template yang sudah disediakan, sehingga memudahkan dalam pembuatan menu Master baru.

# template Sementara 
1. controller
PPID-polinema/Modules/Sisfo/App/Http/Controllers/Template/MasterController.php
2. view 
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/index.blade.php
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/data.blade.php
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/create.blade.php
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/update.blade.php
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/detail.blade.php
- PPID-polinema/Modules/Sisfo/Resources/views/Template/Master/delete.blade.php

# Rancangan Tambah Kolom
manambahkan kolom baru pada tabel web_menu_url yaitu colomn wmu_akses_tabel untuk value tabel yang diakses, dan wmu_kategori_menu (ENUM 'pengajuan'. 'master', 'custom')

# Fungsi
# 1 Colomn wmu_akses_tabel
untuk menyimpan nama tabel yang ingin dibuatkan sebuah menu master
# 2 Colomn wmu_kategori_menu
- Master : untuk menu master yang dibuat menggunakan template master yang sudah disediakan jadi tidak perlu ngoding untuk membuat menu master baru, cukup dengan menambahkan data pada tabel web_menu_url dengan mengisi kolom wmu_akses_tabel dengan nama tabel yang ingin dibuatkan menu master, dan mengisi kolom wmu_kategori_menu dengan 'master'
- Pengajuan : konsepnya sama seperti master tetapi menggunakan template pengajuan tetapi mekanisme ini akan dibuat nanti setelah mekanisme master selesai dibuat, untuk sementara fokus dulu ke mekanisme master
- Custom : untuk menu custom yang dibuat dengan cara manual ngoding seperti biasa, untuk menu dengan kategori ini tidak perlu mengisi kolom wmu_akses_tabel karena menu dengan kategori ini tidak menggunakan template yang sudah disediakan dan wajib mengisi controller_name dengan nama controller yang sudah dibuat untuk menggunakan route dinamis

# Mekanisme template Master
1. menyiapakan sebuah tabel baru yang ingin dibuatkan menu master, misalnya tabel 'm_testing'
2. membuka menu 'management Menu URL'
3. klik tombol 'Tambah Menu URL'
4. mengisi form
- memilih aplikasi nanti value akan masuk ke fk_m_application
- menentukan kategori menu url dengan memilih 'master' pada kolom wmu_kategori_menu
- mengisi value tabel yang sudah dibuat pada wmu_akses_tabel dengan nama tabel yang ingin dibuatkan menu master, misalnya 'm_testing'
- setelah mengisi tabel sistem akan melakukan check apakah tabel tersebut ada didatabse atau tidak, jika tidak ada akan muncul alert bahwa tabel tidak ditemukan, jika ada maka sistem akan membaca berapa banyak colomn yang dimiliki tabel tersebut dan otomatis membuatkan menu master dengan fitur CRUD (Create, Read, Update, Delete) untuk tabel tersebut dengan menggunakan template master yang sudah disediakan, jadi tidak perlu ngoding untuk membuat menu master
* Contoh
1. jika ada 3 kolom dengan 1 kolom pk angka dan auto increment
a. kolom pk increment tidak perlu konfigurasi karena sudah otomatis sebagai primary key
b. kolom 2 dan 3 itu sistem akan membaca tipe data kolom tersebut misalkan type adanya varchar maka akan ada pilihan kita mau beri inputan text atau textarea, jika type int, decimal, big int dll (berhubungan angka) maka opsi yang terbuka adalah inputan type number, jika type date, datetime, timestamp maka opsi yang terbuka adalah inputan type date atau rentang tanggal (date2), jika type enum maka opsi yang terbuka adalah inputan dropdown atau radio button
2. jika ada 3 kolom dan kolo pk tidak auto increment
maka kolom pk tersebut akan mengikuti opsi seperti kolom lainnya, karena tidak ada perbedaan antara kolom pk dengan kolom lainnya
3. adanya inputan nama inputan untuk memberi nama inputan yang akan ditampilkan pada form, karena nama kolom pada database belum tentu sesuai dengan nama inputan yang diinginkan untuk ditampilkan pada form, jadi dengan adanya inputan nama inputan ini kita bisa memberi nama inputan sesuai dengan yang diinginkan untuk ditampilkan pada form
4. adanya kriteria yaitu unique karena ada kondisi kolom itu harus unik seperti kode, email atau pk yang bukan auto increment, dan ada  juga lowecase dan uppercase
5. adanya validasi inputan untuk mengenakan validasi pada inputan seperti wajib diisi atau bersifat opsional, maksimal karakter, minimal karakter
6. dari contoh diatas berarti setiap kolom ada 4 inputam yaitu; Nama inputan, Type Inputan, Kriteria Inputan (opsional), dan validasi inputan (opsional)
- menentukan url menu dengan mengisi wmu_nama
- menentukan apakah url menu tersebut merupakan parent atau child yang jika parent nanti value wmu_parent_id bernilai null (jika memilih kategori menu url 'master' maka menu akan otomatis parent) jadi tidak ada inputan ini karena value wmu_oparent_id akan otomatis null jadi mengatur logika dibackend saja 
- untuk value controller_name otomatis menggunakan Template/MasterController karena sudah dibuatkan template untuk menu master jadi tidak perlu membuat controller baru untuk menu master baru yang akan dibuat, jadi tidak ada inputan ini karena value controller name akan otomatis Template/MasterController jadi mengatur logika dibackend saja
- untuk value module_type otomatis bervalue sisfo
- mengisi wmu_keterangan untuk keterangan menu ini adalah menu apa
5. setelah mengisi form dengan kategori 'master' maka silahkan klik simpan
7. setelah itu silahkan beralih ke menu 'menu management global'
8. pada menu 'menu management global' silahkan klik tambah menu global
9. mengisi form untuk menambahkan menu global
- menginputkan nama default menu
- menginputkan icon harus font awesome contoh 'fa-book'
- menentukan type apakah 'general' atau 'special'
- menentukan kategori menu
- menentukan url menu yang sudah dibuat tadi dengan memilih url menu yang sudah dibuat tadi pada dropdown url menu (kategori sub menu atau menu biasa karena 2 kategori tersebut yang membutuhkan url)
- urutan menu diatur dibackend
- menentukan apakah perlu indicator dengan pilihan 'ya' atau 'tidak' jika ya otomatis terisi 'getBadgeCount' pada kolom wmg_badge_method jika tidak akan bernilai null
- meemilih status menu apakah aktif atau tidak aktif
10 . setelah mengisi form silahkan klik simpan
11 setelah itu ke menu 'menu-management' dan melakukan pemberian akses ke role yang membutuhkan

# mekanisme akses tabel dengan detail
1. untuk menu master tabel yang diperlukan tidak harus 1 bisa 2 dengan adanya fk jadi misalnya ada tabel 'm_testing' yang memiliki fk ke tabel 'm_kategori' maka pada kolom wmu_akses_tabel bisa diisi dengan 'm_testing' saja karena sistem akan membaca fk yang ada pada tabel 'm_testing' yaitu 'fk_m_kategori' dan yang dibikn menu master tetap hanya menu testing
contoh: 
tabel m_kategori
| kategori_id | kategori_kode | kategori_nama |
| ----------- | ------------- | ------------- |
| 1           | WEB           | Website       |
| 2           | MOB           | Mobile        |
tabel m_testing
| testing_id | fk_m_kategori | testing_nama         | testing_hasil |
| ---------- | ------------- | -------------------- | ------------- |
| 1          | 1             | Pengujian Login      | Berhasil      |
| 2          | 2             | Pengujian Registrasi | Berhasil      |

jika tabel yang ingin dibuat menu masternya memiliki kolom fk seperti itu maka konfigurasi inputannya akan jadi seperti berikut:
* ketentuan 1 kolom terdapat 4 inputan yaitu; Nama inputan, Type Inputan, Kriteria Inputan (opsional), dan validasi inputan (opsional) tetapi jika kolom fk terdapat 5 inputan yaitu; Nama inputan, Type Inputan, Kriteria Inputan (opsional), validasi inputan (opsional), dan inputan tambahan untuk isi kolom yang ingin ditampilkan pada dropdown untuk kolom fk tersebut inputan tambahan untuk kolom yang ingin ditampilkan ini adalah dalam bentuk dropdown dari semua kolom yang ada pada tabel yang menjadi referensi fk tersebut kecuali kolom pk dan kolom general yaitu isDeleted, created_by, created_at, updated_by, updated_at, deleted_by, deleted_at 
- untuk kolom testing_id karena itu adalah pk dan auto increment jadi inputannya otomatis sebagai primary key jadi tidak perlu konfigurasi
- untuk kolom fk_m_kategori karena itu adalah fk yang maka opsi type inputannya itu hanya search kemudian kita menginputkan konfigurasi untuk kolom yang ingin ditampilkan misal 'kategori_nama' maka nantinya ketika menu master sudah jadi pada inputan fk inputan bisa diklik dan akan muncul modal dengan colom no, kategori_nama, action (centang):
| kategori_id  | kategori_nama | action |
| -----------  | ------------- |--------|
| 1            | Website       |    ✔   |
| 2            | Mobile        |    ✔   |
catatan: dan ketika memilih konfigurasi kolom yang ingin ditampilkan bisa lebih dari 1 misal 'kategori_kode' dan 'kategori_nama'
- untuk kolom testing_nama karena itu adalah kolom biasa maka opsi type inputannya itu bisa text, textarea tergantung kebutuhan dan keinginan saja
- untuk kolom testing_hasil karena itu adalah kolom biasa maka opsi type inputannya itu bisa text, textarea tergantung kebutuhan dan keinginan saja

# opsi inputan type inputan
1. type kolom char, varchar, text, longtext dan sejenisnya -> opsi type inputan text, textarea
2. type kolom int, decimal, bigint, float dan sejenisnya -> opsi type inputan number (hanya boleh angka)
3. type kolom date, datetime, timestamp dan sejenisnya -> opsi type inputan date atau date2 (rentang tanggal)
4. type kolom enum dan sejenisnya -> opsi type inputan dropdown atau radio button
5. type inputan fk -> opsi type search dengan konfigurasi tambahan untuk kolom yang ingin ditampilkan pada dropdown ketika memilih data fk tersebut

# opsi kriteria inputan (opsional)
1. kriteria inputan harus diisi jika type inputan adalah search
2. kriteria inputan unique bisa dipilih jika kolom tersebut memiliki ketentuan unique seperti kode, email atau pk yang bukan auto increment
3. kriteria inputan lowercase bisa dipilih jika ingin semua inputan pada kolom tersebut menjadi lowercase
4. kriteria inputan uppercase bisa dipilih jika ingin semua inputan pada kolom tersebut menjadi uppercase

# opsi validasi inputan (opsional)
1. validasi inputan wajib diisi jika kolom tersebut tidak boleh kosong
2. validasi inputan maksimal karakter bisa dipilih jika ingin membatasi jumlah karakter yang diinputkan pada kolom tersebut
3. validasi inputan minimal karakter bisa dipilih jika ingin membatasi jumlah karakter minimal yang diinputkan pada kolom tersebut
4. validasi inputan email bisa dipilih jika kolom tersebut harus berisi email yang valid

# Step 1
- Analisis secara mendalam dan teliti mengenai sistem saya ini beserta database dan mekanismenya kemudian analisis ide rancangan saya apakah bisa atau tidak untuk direalisasikan, jika bisa maka lanjut ke step 2, jika tidak bisa maka berikan solusi terbaik untuk merealisasikan ide rancangan saya ini 
* catatan: menu master ini juga menggunakan mekanisme route dinamis seperti menu custom yang sudah ada jadi juga tidak perlu membuat kode di route dan untuk controller_name bervalue Template/MasterController.

# Step 2
sementara saya masih menyiapkan template controller dan view untuk mekanisme menu master dan masih terpikirkan bahwa hanya perlu menambah 2 kolom pada tabel web_menu_url yaitu kolom wmu_akses_tabel dan wmu_kategori_menu untuk merealisasikan ide rancangan ini, nah analisis secara mendalam dan teliti apakah butuh tambahan kolom lain atau bahkan tabel baru untuk merealisasikan ide rancangan saya ini dan analisis apakah butuh tambahan template file lagi seperti model atau file js yang ditujukan untuk merealisasikan ide rancangan saya ini contohnya untuk mekanisme akses tabel dengan detail, opsi inputan type inputan, opsi kriteria inputan, dan opsi validasi inputan yang sudah saya jelaskan diatas itu dan kebutuhan lainnya yang diperlukan untuk merealisasikan ide rancangan saya ini, jika butuh maka berikan saya konsep dan rekomendasinya

# Step 3
- setelah semua kebutuhan untuk merealisasikan ide rancangan saya ini sudah terpenuhi maka buatkanlah sebuah dokumentasi yang menjelaskan secara rinci mengenai mekanisme pembuatan menu master tanpa ngoding ini mulai dari penjelasan setiap kolom yang diperlukan, penjelasan setiap opsi yang ada, dan langkah-langkah untuk membuat menu master baru menggunakan mekanisme ini, dokumentasi ini akan sangat membantu untuk memahami dan menggunakan mekanisme ini dengan baik, jadi buatlah dokumentasi ini sejelas dan serinci mungkin

# Step 4
- setelah dokumentasi selesai maka eksekusi kode program template yang diperlukan dimulai dari migration dan seeder untuk menambah kolom baru pada tabel web_menu_url atau menambah kolom ditabel lain atau menambah tabel baru jika memang diperlukan (sesuai analisis kebutuhan pada step 1 dan 2) dan untuk seeder nya untuk kolom wmu_kategori_menu untuk data yang ada didatabase buat default value 'custom' karena untuk sementara menu yang sudah ada ini menggunakan mekanisme manual ngoding jadi kategorinya adalah custom

# Step 5
- setelah itu eksekusi kode program untuk template controller dan view untuk mekanisme menu master yang sudah disiapkan sebelumnya

catatan: controller harus memiliki function yang sudah standart untuk info lebih detail fucntion apa saja anda bisa melihat pada aturan-route-dinamis.instructions.md karena mekanisme menu master ini menggunakan mekanisme route dinamis seperti menu custom yang sudah ada jadi function yang dibuat pada controller untuk menu master ini juga harus mengikuti standar function pada mekanisme route dinamis
untuk views sudah saya sediakan template views standart untuk mekanisme menu master ini yaitu index.blade.php, data.blade.php, create.blade.php, update.blade.php, detail.blade.php, dan delete.blade.php (tidak boleh lebih) jadi tinggal menyesuaikan saja dengan kebutuhan mekanisme menu master ini

# Step 6
buatkan juga kode program template untuk kebutuhan lain yang diperlukan untuk merealisasikan ide rancangan saya ini sesuai dengan analisis kebutuhan pada step 1 dan 2 (jika terdapat kebutuhan lain yang diperlukan untuk merealisasikan ide rancangan saya ini)

# Step 7
tata ulang proses crud pada menu 'management menu url' untuk menyesuaikan dengan mekanisme baru ini, karena pada mekanisme baru ini ada 3 kategori menu url yaitu master, pengajuan, dan custom
- jika memilih kategori menu url 'master' maka alurnya kira kira akan seperti step Mekanisme template Master yang sudah saya jelaskan diatas.
- jika memilih kategori menu url 'pengajuan' maka akan muncul info bahwa mekanisme untuk kategori ini masih dalam tahap pengembangan.
- jika memilih kategori menu url 'custom' maka alurnya akan seperti mekanisme manual ngoding:
1. memilih aplikasi nanti value akan masuk ke fk_m_application
2. memilih module type 'user' atau 'sisfo'
3. menentukan apakah url menu tersebut merupakan parent atau child yang jika parent nanti value wmu_parent_id bernilai null jadi mengatur logika dibackend saja, jika merupakan child maka akan muncul inputan dropdown untuk memilih parent menu yang sudah ada
4. menentukan url menu dengan mengisi wmu_nama
5. jika module type adalah sisfo menentukan controller name dengan mengisi controller_name dengan nama controller yang sudah dibuat untuk menggunakan route dinamis jika module type user maka controller name otomatis terisi null jadi tidak ada inputan controller name karena menu dengan module type user ini tidak menggunakan route dinamis
6. mengisi wmu_keterangan untuk keterangan menu ini adalah menu apa
7. jika custom untuk sementara ini tidak perlu mengisi kolom wmu_akses_tabel jadi value colomn tersebut otomatis null dan tidak ada inputan untuk kolom wmu_akses_tabel
8.kemudian setelah mengisi form silahkan klik simpan

# Step 8
tata ulang proses crud pada menu 'menu management global' untuk menyesuaikan dengan mekanisme baru ini untuk alur pengisian kira kira sama seperti Mekanisme template Master yang sudah saya jelaskan diatas

