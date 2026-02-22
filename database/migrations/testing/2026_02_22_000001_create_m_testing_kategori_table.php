<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_testing_kategori', function (Blueprint $table) {
            $table->integer('m_testing_kategori_id')->primary();
            $table->string('tk_kode', 50);
            $table->string('tk_nama', 100);
            $table->text('tk_keterangan')->nullable();
            
            $table->tinyInteger('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->string('created_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_testing_kategori');
    }
};
