<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_ip_dinamis_tabel', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('ip_dinamis_tabel_id')->unsigned()->autoIncrement();
            $table->string('ip_nama_submenu', 100);
            $table->string('ip_judul', 100);
            $table->text('ip_deskripsi');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'm_ip_dinamis_tabel_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_ip_dinamis_tabel');
    }
};
