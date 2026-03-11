<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_ip_upload_konten', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('ip_upload_konten_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_ip_dinamis_konten')->unsigned()->nullable();
            $table->string('uk_judul_konten', 200);
            $table->enum('uk_tipe_upload_konten', ['link', 'file'])->default('file');
            $table->string('uk_dokumen_konten', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_upload_konten_isDeleted');
            $table->index('fk_m_ip_dinamis_konten', 'fk_t_ip_upload_konten_m_ip_dinamis_konten1_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_ip_upload_konten');
    }
};
