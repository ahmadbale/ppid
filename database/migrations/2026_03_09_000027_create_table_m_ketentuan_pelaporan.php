<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('m_ketentuan_pelaporan', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('ketentuan_pelaporan_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_kategori_form')->unsigned();
            $table->string('kp_judul', 100);
            $table->longText('kp_konten');
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            // Note: index isDeleted adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 'm_ketentuan_pelaporan_isDeleted');
            $table->index('fk_m_kategori_form', 'm_ketentuan_pelaporan_fk_m_kategori_form_idx');

            $table->foreign('fk_m_kategori_form', 'm_ketentuan_pelaporan_fk_m_kategori_form')
                ->references('kategori_form_id')->on('m_kategori_form')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('m_ketentuan_pelaporan', function (Blueprint $table) {
            $table->dropForeign('m_ketentuan_pelaporan_fk_m_kategori_form');
        });

        Schema::dropIfExists('m_ketentuan_pelaporan');
    }
};
