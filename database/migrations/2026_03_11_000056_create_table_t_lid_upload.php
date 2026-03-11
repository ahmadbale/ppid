<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_lid_upload', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('lid_upload_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_li_dinamis')->unsigned()->nullable();
            $table->enum('lid_upload_type', ['link', 'file']);
            $table->string('lid_upload_value', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_upload_prosedur_isDeleted');
            $table->index('fk_m_li_dinamis', 't_upload_prosedur_fk_m_kategori_prosedur_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_lid_upload');
    }
};
