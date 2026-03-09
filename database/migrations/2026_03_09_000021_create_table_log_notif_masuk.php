<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_notif_masuk', function (Blueprint $table) {
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->integer('notif_masuk_id')->unsigned()->autoIncrement();
            $table->string('notif_masuk_kategori', 100);
            $table->integer('notif_masuk_form_id');
            $table->string('notif_masuk_pesan', 255);
            $table->string('notif_masuk_dibaca_oleh', 50)->nullable();
            $table->timestamp('notif_masuk_dibaca_tgl')->nullable();
            $table->tinyInteger('isDeleted')->default(0);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->string('deleted_by', 50)->nullable();
            $table->timestamp('deleted_at')->nullable();

            $table->index('isDeleted', 'log_notif_admin_isDeleted');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_notif_masuk');
    }
};
