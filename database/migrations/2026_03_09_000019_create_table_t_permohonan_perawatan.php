<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_permohonan_perawatan', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('permohonan_perawatan_id')->unsigned()->autoIncrement();
            $table->enum('pp_kategori_aduan', ['online', 'offline', 'lapor.go.id'])->default('online');
            $table->string('pp_bukti_aduan', 100)->nullable();
            $table->string('pp_nama_pengguna', 255);
            $table->string('pp_no_hp_pengguna', 20);
            $table->string('pp_email_pengguna', 100);
            $table->string('pp_unit_kerja', 100);
            $table->string('pp_perawatan_yang_diusulkan', 255);
            $table->string('pp_keluhan_kerusakan', 255);
            $table->string('pp_lokasi_perawatan', 255);
            $table->string('pp_foto_kondisi', 100)->nullable();
            $table->enum('pp_status', ['Masuk', 'Verifikasi', 'Disetujui', 'Ditolak'])->default('Masuk');
            $table->string('pp_jawaban', 255)->nullable();
            $table->string('pp_alasan_penolakan', 255)->nullable();
            $table->string('pp_sudah_dibaca', 255)->nullable();
            $table->timestamp('pp_tanggal_dibaca')->nullable();
            $table->string('pp_verifikasi', 255)->nullable();
            $table->timestamp('pp_tanggal_verifikasi')->nullable();
            $table->string('pp_review_sudah_dibaca', 255)->nullable();
            $table->timestamp('pp_review_tanggal_dibaca')->nullable();
            $table->string('pp_dijawab', 255)->nullable();
            $table->timestamp('pp_tanggal_dijawab')->nullable();
            $table->tinyInteger('pp_verifikasi_isDeleted')->nullable()->default(0);
            $table->tinyInteger('pp_review_isDeleted')->nullable()->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_pp_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_permohonan_perawatan');
    }
};
