<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_whatsapp', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('log_whatsapp_id')->unsigned()->autoIncrement();
            $table->string('log_whatsapp_status', 255);
            $table->string('log_whatsapp_nama_pengirim', 255);
            $table->string('log_whatsapp_nomor_tujuan', 255);
            $table->text('log_whatsapp_pesan');
            $table->enum('log_whatsapp_delivery_status', ['Pending', 'Sent', 'Error'])->default('Pending');
            $table->timestamp('log_whatsapp_tanggal_dikirim');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_whatsapp');
    }
};
