<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_footer', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('footer_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_kategori_footer')->unsigned()->nullable();
            $table->string('f_judul_footer', 100);
            $table->string('f_icon_footer', 100)->nullable();
            $table->string('f_url_footer', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_footer_isDeleted');
            $table->index('fk_m_kategori_footer', 't_footer_fk_m_kategori_footer_idx');

            $table->foreign('fk_m_kategori_footer', 'fk_t_footer_m_kategori_footer')
                ->references('kategori_footer_id')->on('m_kategori_footer')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_footer', function (Blueprint $table) {
            $table->dropForeign('fk_t_footer_m_kategori_footer');
        });

        Schema::dropIfExists('t_footer');
    }
};
