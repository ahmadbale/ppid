<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_wbs', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('wbs_id')->unsigned()->autoIncrement();
            $table->enum('wbs_kategori_aduan', ['online', 'offline', 'lapor.go.id'])->default('online');
            $table->string('wbs_bukti_aduan', 100)->nullable();
            $table->string('wbs_nama_tanpa_gelar', 255);
            $table->string('wbs_nik_pengguna', 20);
            $table->string('wbs_upload_nik_pengguna', 100);
            $table->string('wbs_email_pengguna', 100);
            $table->string('wbs_no_hp_pengguna', 20);
            $table->string('wbs_jenis_laporan', 100);
            $table->string('wbs_yang_dilaporkan', 255);
            $table->enum('wbs_jabatan', ['Staff', 'Dosen', 'Tidak tahu']);
            $table->dateTime('wbs_waktu_kejadian');
            $table->string('wbs_lokasi_kejadian', 255);
            $table->string('wbs_kronologis_kejadian', 255);
            $table->string('wbs_bukti_pendukung', 255);
            $table->string('wbs_catatan_tambahan', 255)->nullable();
            $table->enum('wbs_status', ['Masuk', 'Verifikasi', 'Disetujui', 'Ditolak'])->default('Masuk');
            $table->string('wbs_jawaban', 255)->nullable();
            $table->string('wbs_alasan_penolakan', 255)->nullable();
            $table->string('wbs_sudah_dibaca', 255)->nullable();
            $table->timestamp('wbs_tanggal_dibaca')->nullable();
            $table->string('wbs_verifikasi', 255)->nullable();
            $table->timestamp('wbs_tanggal_verifikasi')->nullable();
            $table->string('wbs_review_sudah_dibaca', 255)->nullable();
            $table->timestamp('wbs_review_tanggal_dibaca')->nullable();
            $table->string('wbs_dijawab', 255)->nullable();
            $table->timestamp('wbs_tanggal_dijawab')->nullable();
            $table->tinyInteger('wbs_verifikasi_isDeleted')->nullable()->default(0);
            $table->tinyInteger('wbs_review_isDeleted')->nullable()->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_wbs_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_wbs');
    }
};
