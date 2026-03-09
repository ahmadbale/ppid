<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pengaduan_masyarakat', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('pengaduan_masyarakat_id')->unsigned()->autoIncrement();
            $table->enum('pm_kategori_aduan', ['online', 'offline', 'lapor.go.id'])->default('online');
            $table->string('pm_bukti_aduan', 100)->nullable();
            $table->string('pm_nama_tanpa_gelar', 255);
            $table->string('pm_nik_pengguna', 20);
            $table->string('pm_upload_nik_pengguna', 100);
            $table->string('pm_email_pengguna', 100);
            $table->string('pm_no_hp_pengguna', 20);
            $table->string('pm_jenis_laporan', 100);
            $table->string('pm_yang_dilaporkan', 255);
            $table->enum('pm_jabatan', ['Staff', 'Dosen', 'Tidak tahu']);
            $table->dateTime('pm_waktu_kejadian');
            $table->string('pm_lokasi_kejadian', 255);
            $table->string('pm_kronologis_kejadian', 255);
            $table->string('pm_bukti_pendukung', 255);
            $table->string('pm_catatan_tambahan', 255)->nullable();
            $table->enum('pm_status', ['Masuk', 'Verifikasi', 'Disetujui', 'Ditolak'])->default('Masuk');
            $table->string('pm_jawaban', 255)->nullable();
            $table->string('pm_alasan_penolakan', 255)->nullable();
            $table->string('pm_sudah_dibaca', 255)->nullable();
            $table->timestamp('pm_tanggal_dibaca')->nullable();
            $table->string('pm_verifikasi', 255)->nullable();
            $table->timestamp('pm_tanggal_verifikasi')->nullable();
            $table->string('pm_review_sudah_dibaca', 255)->nullable();
            $table->timestamp('pm_review_tanggal_dibaca')->nullable();
            $table->string('pm_dijawab', 255)->nullable();
            $table->timestamp('pm_tanggal_dijawab')->nullable();
            $table->tinyInteger('pm_verifikasi_isDeleted')->nullable()->default(0);
            $table->tinyInteger('pm_review_isDeleted')->nullable()->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_pm_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_pengaduan_masyarakat');
    }
};
