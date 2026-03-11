<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_upload_ps', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('upload_ps_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_penyelesaian_sengketa')->unsigned()->nullable();
            $table->enum('kategori_upload_ps', ['link', 'file']);
            $table->string('upload_ps', 100);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_upload_ps_isDeleted');
            $table->index('fk_m_penyelesaian_sengketa', 't_upload_ps_fk_m_penyelesaian_sengketa_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_upload_ps');
    }
};
