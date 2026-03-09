<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_notif_verif', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->bigInteger('notif_verif_id')->unsigned()->autoIncrement();
            $table->string('notif_verif_kategori', 100);
            $table->bigInteger('notif_verif_form_id')->unsigned();
            $table->text('notif_verif_pesan');
            $table->string('notif_verif_dibaca_oleh', 255)->nullable();
            $table->timestamp('notif_verif_dibaca_tgl')->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index(['notif_verif_kategori', 'notif_verif_form_id'], 'log_notif_verif_notif_verif_kategori_notif_verif_form_id_index');
            $table->index('isDeleted', 'log_notif_verif_isdeleted_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_notif_verif');
    }
};
