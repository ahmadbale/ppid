<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kategori_akses', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('kategori_akses_id')->unsigned()->autoIncrement();
            $table->string('mka_judul_kategori', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'm_kategori_akses_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kategori_akses');
    }
};
