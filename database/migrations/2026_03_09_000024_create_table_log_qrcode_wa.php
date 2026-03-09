<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_qrcode_wa', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('log_qrcode_wa_id')->unsigned()->autoIncrement();
            $table->string('log_qrcode_wa_nomor_pengirim', 20)->nullable();
            $table->string('log_qrcode_wa_user_scan', 255)->nullable();
            $table->text('log_qrcode_wa_ha_scan')->nullable();
            $table->timestamp('log_qrcode_wa_tanggal_scan')->nullable();
            $table->tinyInteger('is_confirmed')->nullable()->default(0);
            $table->tinyInteger('pending_confirmation')->nullable()->default(0);
            $table->dateTime('confirmation_expires_at')->nullable();
            $table->tinyInteger('isDeleted')->nullable()->default(0);
            $table->timestamp('deleted_at')->nullable();

            // Note: index ini adalah INVISIBLE index di MySQL, tidak didukung native oleh Laravel Blueprint
            $table->index('isDeleted', 'log_barcode_wa_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_qrcode_wa');
    }
};
