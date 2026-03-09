<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_permohonan_informasi', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('permohonan_informasi_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_form_pi_diri_sendiri')->unsigned()->nullable();
            $table->integer('fk_t_form_pi_orang_lain')->unsigned()->nullable();
            $table->integer('fk_t_form_pi_organisasi')->unsigned()->nullable();
            $table->enum('pi_kategori_pemohon', ['Diri Sendiri', 'Orang Lain', 'Organisasi']);
            $table->enum('pi_kategori_aduan', ['online', 'offline', 'lapor.go.id'])->default('online');
            $table->string('pi_bukti_aduan', 100)->nullable();
            $table->string('pi_informasi_yang_dibutuhkan', 255);
            $table->string('pi_alasan_permohonan_informasi', 255);
            $table->enum('pi_sumber_informasi', ['Pertanyaan Langsung Pemohon', 'Website / Media Sosial Milik Polinema', 'Website / Media Sosial Bukan Milik Polinema']);
            $table->string('pi_alamat_sumber_informasi', 255);
            $table->enum('pi_status', ['Masuk', 'Verifikasi', 'Disetujui', 'Ditolak'])->default('Masuk');
            $table->string('pi_jawaban', 255)->nullable();
            $table->string('pi_alasan_penolakan', 255)->nullable();
            $table->string('pi_sudah_dibaca', 255)->nullable();
            $table->timestamp('pi_tanggal_dibaca')->nullable();
            $table->string('pi_verifikasi', 255)->nullable();
            $table->timestamp('pi_tanggal_verifikasi')->nullable();
            $table->string('pi_review_sudah_dibaca', 255)->nullable();
            $table->timestamp('pi_review_tanggal_dibaca')->nullable();
            $table->string('pi_dijawab', 255)->nullable();
            $table->timestamp('pi_tanggal_dijawab')->nullable();
            $table->tinyInteger('pi_verifikasi_isDeleted')->nullable()->default(0);
            $table->tinyInteger('pi_review_isDeleted')->nullable()->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 't_pi_isDeleted');
            $table->index('fk_t_form_pi_diri_sendiri', 't_permohonan_informasi_fk_t_form_pi_perorangan_idx');
            $table->index('fk_t_form_pi_organisasi', 't_permohonan_informasi_fk_t_form_pi_organisasi_idx');
            $table->index('fk_t_form_pi_orang_lain', 't_permohonan_informasi_fk_t_form_pi_orang_lain_idx');

            $table->foreign('fk_t_form_pi_diri_sendiri', 't_permohonan_informasi_fk_t_form_pi_diri_sendiri')
                ->references('form_pi_diri_sendiri_id')->on('t_form_pi_diri_sendiri')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_t_form_pi_orang_lain', 't_permohonan_informasi_fk_t_form_pi_orang_lain')
                ->references('form_pi_orang_lain_id')->on('t_form_pi_orang_lain')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_t_form_pi_organisasi', 't_permohonan_informasi_fk_t_form_pi_organisasi')
                ->references('form_pi_organisasi_id')->on('t_form_pi_organisasi')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_permohonan_informasi', function (Blueprint $table) {
            $table->dropForeign('t_permohonan_informasi_fk_t_form_pi_diri_sendiri');
            $table->dropForeign('t_permohonan_informasi_fk_t_form_pi_orang_lain');
            $table->dropForeign('t_permohonan_informasi_fk_t_form_pi_organisasi');
        });

        Schema::dropIfExists('t_permohonan_informasi');
    }
};
