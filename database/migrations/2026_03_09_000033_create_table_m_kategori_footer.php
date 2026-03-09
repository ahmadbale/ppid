<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_kategori_footer', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('kategori_footer_id')->unsigned()->autoIncrement();
            $table->string('kt_footer_kode', 5);
            $table->string('kt_footer_nama', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'm_kategori_footer_isDeleted');
            $table->index('kt_footer_kode', 'kategori_footer_kode_UNIQUE');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_kategori_footer');
    }
};
