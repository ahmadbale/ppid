<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_li_dinamis', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('li_dinamis_id')->unsigned()->autoIncrement();
            $table->string('li_dinamis_kode', 20);
            $table->string('li_dinamis_nama', 255);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->unique('li_dinamis_kode', 'kategori_prosedur_kode_UNIQUE');
            $table->index('isDeleted', 'm_kategori_prosedur_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_li_dinamis');
    }
};
