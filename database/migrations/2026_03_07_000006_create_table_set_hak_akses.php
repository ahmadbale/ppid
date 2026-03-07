<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('set_hak_akses', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('set_hak_akses_id')->unsigned()->autoIncrement();
            $table->integer('fk_web_menu')->unsigned();
            $table->integer('ha_pengakses')->unsigned();
            $table->tinyInteger('ha_menu')->default(0);
            $table->tinyInteger('ha_view')->default(0);
            $table->tinyInteger('ha_create')->default(0);
            $table->tinyInteger('ha_update')->default(0);
            $table->tinyInteger('ha_delete')->default(0);
            $table->tinyInteger('isDeleted')->default(0);
            $table->string('created_by', 30);
            $table->timestamp('created_at')->useCurrent();
            $table->string('updated_by', 30)->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->string('deleted_by', 30)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'set_hak_akses_isDeleted');
            $table->index('fk_web_menu', 'fk_set_hak_akses_web_menu1_idx');

            $table->foreign('fk_web_menu', 'fk_set_hak_akses_web_menu1')
                ->references('web_menu_id')->on('web_menu')
                ->onDelete('RESTRICT')->onUpdate('CASCADE');
        });
    }

    public function down(): void
    {
        Schema::table('set_hak_akses', function (Blueprint $table) {
            $table->dropForeign('fk_set_hak_akses_web_menu1');
        });

        Schema::dropIfExists('set_hak_akses');
    }
};
