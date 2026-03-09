<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_lhkpn', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('lhkpn_id')->unsigned()->autoIncrement();
            $table->year('lhkpn_tahun');
            $table->string('lhkpn_judul_informasi', 200);
            $table->text('lhkpn_deskripsi_informasi');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('lhkpn_tahun', 'tahun_lhkpn_index');
            $table->index('isDeleted', 't_lhkpn_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('m_lhkpn');
    }
};
