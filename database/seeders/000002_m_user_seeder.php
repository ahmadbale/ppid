<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Seeder000002MUser extends Seeder
{
    public function run(): void
    {
        $data = [
            ['user_id'=>1,'password'=>'$2y$12$LDaYqnIoRpSEp0jkOwobNeLJaPvr9PC7Pd5WL58dyXC7OzV863Aiy','nama_pengguna'=>'Gelby Firmansyah','alamat_pengguna'=>'Jl. Joyo Raharjo Gg 9b','no_hp_pengguna'=>'08111','email_pengguna'=>'gelbifirmansyah12@gmail.com','pekerjaan_pengguna'=>'Super Admin','nik_pengguna'=>'11111','upload_nik_pengguna'=>'upload_nik/leIm8Mf54FVTSfkEXkFkxQvWDNgaorrPq9KknxxD.png','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-02 13:11:03','updated_by'=>null,'updated_at'=>'2025-03-02 13:11:03','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>2,'password'=>'$2y$12$ypOk.QdDXR7Xu7ZzRiweQu95ybzrJdkgLGvvK0YgWhx401if4GLHS','nama_pengguna'=>'Lionel Messi','alamat_pengguna'=>'Jl. Jenderal Ahmad Yani','no_hp_pengguna'=>'08222','email_pengguna'=>'messi@gmail.com','pekerjaan_pengguna'=>'Administrator','nik_pengguna'=>'22222','upload_nik_pengguna'=>'upload_nik/LEQArYs2nWb3rLVa9TWJNldDGY4CKAb9HIjZlpMu.png','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-02 13:43:48','updated_by'=>null,'updated_at'=>'2025-03-02 13:43:48','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>3,'password'=>'$2y$12$CP1ZcWafzmkLZ9qSfwzL4ucmgW8wKRS8132R0lw76DpANC0N6bPnu','nama_pengguna'=>'Zaenal Arifin','alamat_pengguna'=>'Jl veteran','no_hp_pengguna'=>'08333','email_pengguna'=>'zaenal@gmail.com','pekerjaan_pengguna'=>'Verifikator','nik_pengguna'=>'33333','upload_nik_pengguna'=>'upload_nik/EL19XyH2gUB43gr3vTX6amEERGRv3MucbOak9dmZ.png','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-02 13:45:50','updated_by'=>null,'updated_at'=>'2025-03-02 13:45:50','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>4,'password'=>'$2y$12$xE/48WBW.HID4GdFjfNb/udz.n84rPh79gqRNxaKrxo3ZrUoO7JfG','nama_pengguna'=>'Agus Subianto','alamat_pengguna'=>'Jl. Mergan','no_hp_pengguna'=>'08444','email_pengguna'=>'agus@gmail.com','pekerjaan_pengguna'=>'Manajemen dan Pimpinan Unit','nik_pengguna'=>'44444','upload_nik_pengguna'=>'upload_nik/U7sRW4WOAAYd6WrL84vj5xRygFTzui4n3AFKrAlK.jpeg','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-02 13:48:16','updated_by'=>null,'updated_at'=>'2025-03-02 13:48:16','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>5,'password'=>'$2y$12$wWtwjSKCumd3sjwoSEk9/unqPa5cF8VL7Tw2ThMZUeUoyQyRE3F.e','nama_pengguna'=>'Ahmad Isroqi Firdaus','alamat_pengguna'=>'Jl Joyo Tambaksari','no_hp_pengguna'=>'085804049240','email_pengguna'=>'isroqiaja@gmail.com','pekerjaan_pengguna'=>'manager','nik_pengguna'=>'55555','upload_nik_pengguna'=>'upload_nik/xe2xBAktKAoOe7a6ZrioXnZk3MrdHFA8CtaSkwOy.png','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-02 13:50:09','updated_by'=>null,'updated_at'=>'2025-03-02 13:50:09','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>6,'password'=>'$2y$12$pxLI8p62FSEbND0YAIyytOuAyUt.XlcugPOOSh7Zv7DuSypbanCJC','nama_pengguna'=>'Cristiano Ronaldo','alamat_pengguna'=>'Jl kali waron','no_hp_pengguna'=>'08777','email_pengguna'=>'ronaldo@gmail.com','pekerjaan_pengguna'=>'Administrator','nik_pengguna'=>'77777','upload_nik_pengguna'=>'upload_nik/U8jcbHARy4v1lJazryisDOEtasNk7zpFOW9IAHcC.jpg','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-10 04:32:09','updated_by'=>null,'updated_at'=>'2025-03-10 04:32:09','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>7,'password'=>'$2y$12$PNghSiqA.PCleNHNFklliOvlv6pTWf6knHoKuk96paoUpIaL4SVeu','nama_pengguna'=>'Balee','alamat_pengguna'=>'perumahan','no_hp_pengguna'=>'12345','email_pengguna'=>'ahmadbale@gmail.com','pekerjaan_pengguna'=>'pelajar','nik_pengguna'=>'0000000000000000','upload_nik_pengguna'=>'upload_nik/GLdGOmMD6vKIajuYrXq4Udyw8j0bKVIqf1iCXIWO.jpg','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'System','created_at'=>'2025-03-20 21:06:21','updated_by'=>null,'updated_at'=>'2025-03-20 21:06:21','deleted_by'=>null,'deleted_at'=>null],
            ['user_id'=>8,'password'=>'$2y$12$6IgWFl8QOEH6fNLRRncC0uMufnCPkIqnjOvOlLErqgtZhAh5C8Lx.','nama_pengguna'=>'Abdus Sallam','alamat_pengguna'=>'Mojokerto','no_hp_pengguna'=>'083166441802','email_pengguna'=>'arekmoker65@gmail.com','pekerjaan_pengguna'=>'Web Development','nik_pengguna'=>'1234567891234567','upload_nik_pengguna'=>'upload_nik/g07yEl8ygKiq1Ab9LfslxGESTxk0sPIDd85XkVpo.jpg','foto_profil'=>null,'isDeleted'=>0,'created_by'=>'Gelby F.','created_at'=>'2025-06-23 10:29:43','updated_by'=>null,'updated_at'=>'2025-06-23 10:29:43','deleted_by'=>null,'deleted_at'=>null],
        ];

        DB::table('m_user')->insertOrIgnore($data);
    }
}
