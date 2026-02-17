<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('log_notif_verif', function (Blueprint $table) {
            $table->id('notif_verif_id');
            $table->string('notif_verif_kategori', 100);
            $table->unsignedBigInteger('notif_verif_form_id');
            $table->text('notif_verif_pesan');
            $table->string('notif_verif_dibaca_oleh', 255)->nullable();
            $table->timestamp('notif_verif_dibaca_tgl')->nullable();
            $table->boolean('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->string('deleted_by', 255)->nullable();
            $table->timestamp('deleted_at')->nullable();
            
            $table->index(['notif_verif_kategori', 'notif_verif_form_id']);
            $table->index('isDeleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_notif_verif');
    }
};
