<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rename table
        Schema::rename('log_notif_admin', 'log_notif_masuk');

        // Alter columns
        Schema::table('log_notif_masuk', function (Blueprint $table) {
            // Rename columns
            $table->renameColumn('notif_admin_id', 'notif_masuk_id');
            $table->renameColumn('notif_admin_form_id', 'notif_masuk_form_id');
            $table->renameColumn('kategori_notif_admin', 'notif_masuk_kategori');
            $table->renameColumn('pesan_notif_admin', 'notif_masuk_pesan');
            $table->renameColumn('sudah_dibaca_notif_admin', 'notif_masuk_dibaca_tgl');
        });

        // Add new column and modify existing
        Schema::table('log_notif_masuk', function (Blueprint $table) {
            // Add column notif_masuk_dibaca_oleh after notif_masuk_pesan
            $table->string('notif_masuk_dibaca_oleh', 50)->nullable()->after('notif_masuk_pesan');
            
            // Modify deleted_by to varchar(50)
            $table->string('deleted_by', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Remove new column
        Schema::table('log_notif_masuk', function (Blueprint $table) {
            $table->dropColumn('notif_masuk_dibaca_oleh');
        });

        // Rename columns back
        Schema::table('log_notif_masuk', function (Blueprint $table) {
            $table->renameColumn('notif_masuk_id', 'notif_admin_id');
            $table->renameColumn('notif_masuk_form_id', 'notif_admin_form_id');
            $table->renameColumn('notif_masuk_kategori', 'kategori_notif_admin');
            $table->renameColumn('notif_masuk_pesan', 'pesan_notif_admin');
            $table->renameColumn('notif_masuk_dibaca_tgl', 'sudah_dibaca_notif_admin');
        });

        // Rename table back
        Schema::rename('log_notif_masuk', 'log_notif_admin');
    }
};
