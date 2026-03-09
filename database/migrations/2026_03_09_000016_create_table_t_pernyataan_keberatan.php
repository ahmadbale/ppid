<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_pernyataan_keberatan', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('pernyataan_keberatan_id')->unsigned()->autoIncrement();
            $table->integer('fk_t_form_pk_diri_sendiri')->unsigned()->nullable();
            $table->integer('fk_t_form_pk_orang_lain')->unsigned()->nullable();
            $table->enum('pk_kategori_pemohon', ['Diri Sendiri', 'Orang Lain']);
            $table->enum('pk_kategori_aduan', ['online', 'offline', 'lapor.go.id'])->default('online');
            $table->string('pk_bukti_aduan', 100)->nullable();
            $table->string('pk_alasan_pengajuan_keberatan', 255);
            $table->string('pk_kasus_posisi', 255);
            $table->enum('pk_status', ['Masuk', 'Verifikasi', 'Disetujui', 'Ditolak'])->default('Masuk');
            $table->string('pk_jawaban', 255)->nullable();
            $table->string('pk_alasan_penolakan', 255)->nullable();
            $table->string('pk_sudah_dibaca', 255)->nullable();
            $table->timestamp('pk_tanggal_dibaca')->nullable();
            $table->string('pk_verifikasi', 255)->nullable();
            $table->timestamp('pk_tanggal_verifikasi')->nullable();
            $table->string('pk_review_sudah_dibaca', 255)->nullable();
            $table->timestamp('pk_review_tanggal_dibaca')->nullable();
            $table->string('pk_dijawab', 255)->nullable();
            $table->timestamp('pk_tanggal_dijawab')->nullable();
            $table->tinyInteger('pk_verifikasi_isDeleted')->nullable()->default(0);
            $table->tinyInteger('pk_review_isDeleted')->nullable()->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_pk_isDeleted');
            $table->index('fk_t_form_pk_orang_lain', 't_pernyataan_keberatan_fk_t_form_pk_orang_lain_idx');
            $table->index('fk_t_form_pk_diri_sendiri', 't_pernyataan_keberatan_fk_t_form_pk_diri_sendiri');

            $table->foreign('fk_t_form_pk_diri_sendiri', 't_pernyataan_keberatan_fk_t_form_pk_diri_sendiri')
                ->references('form_pk_diri_sendiri_id')->on('t_form_pk_diri_sendiri')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');

            $table->foreign('fk_t_form_pk_orang_lain', 't_pernyataan_keberatan_fk_t_form_pk_orang_lain')
                ->references('form_pk_orang_lain_id')->on('t_form_pk_orang_lain')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_pernyataan_keberatan', function (Blueprint $table) {
            $table->dropForeign('t_pernyataan_keberatan_fk_t_form_pk_diri_sendiri');
            $table->dropForeign('t_pernyataan_keberatan_fk_t_form_pk_orang_lain');
        });

        Schema::dropIfExists('t_pernyataan_keberatan');
    }
};
