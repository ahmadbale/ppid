<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_detail_lhkpn', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('detail_lhkpn_id')->unsigned()->autoIncrement();
            $table->integer('fk_m_lhkpn')->unsigned()->nullable();
            $table->string('dl_nama_karyawan', 100);
            $table->string('dl_file_lhkpn', 100)->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 50);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 50)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 't_detail_lhkpn_isDeleted');
            $table->index('fk_m_lhkpn', 't_detail_lhkpn_fk_m_lhkpn');

            $table->foreign('fk_m_lhkpn', 'fk_t_detail_lhkpn_m_lhkpn')
                ->references('lhkpn_id')->on('m_lhkpn')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('t_detail_lhkpn', function (Blueprint $table) {
            $table->dropForeign('fk_t_detail_lhkpn_m_lhkpn');
        });

        Schema::dropIfExists('t_detail_lhkpn');
    }
};
