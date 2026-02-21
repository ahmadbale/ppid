<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat tabel web_menu_field_config untuk menyimpan 
     * konfigurasi field setiap menu master (Template Master System)
     */
    public function up(): void
    {
        Schema::create('web_menu_field_config', function (Blueprint $table) {
            // Primary Key (konsistensi: nama_tabel_id)
            $table->integer('web_menu_field_config_id')
                ->autoIncrement()
                ->comment('Primary Key');
            
            // Foreign Key ke web_menu_url (signed integer seperti web_menu_url_id)
            $table->integer('fk_web_menu_url')
                ->comment('Relasi ke tabel web_menu_url');
            
            // Field Identification
            $table->string('wmfc_column_name', 100)
                ->comment('Nama kolom di tabel database (e.g., testing_nama)');
            
            $table->string('wmfc_field_label', 255)
                ->comment('Label yang ditampilkan di form (e.g., Nama Testing)');
            
            // Field Type & Configuration
            $table->string('wmfc_field_type', 50)
                ->comment('Type input: text, textarea, number, date, date2, dropdown, radio, search');
            
            $table->json('wmfc_criteria')
                ->nullable()
                ->comment('Kriteria field: {"unique": true, "case": "uppercase"}');
            
            $table->json('wmfc_validation')
                ->nullable()
                ->comment('Validasi: {"required": true, "max": 100, "min": 3, "email": false}');
            
            // Foreign Key Configuration (untuk type = search/dropdown)
            $table->string('wmfc_fk_table', 100)
                ->nullable()
                ->comment('Tabel referensi FK (e.g., m_kategori)');
            
            $table->string('wmfc_fk_pk_column', 100)
                ->nullable()
                ->comment('Kolom PK di tabel referensi (e.g., kategori_id)');
            
            $table->json('wmfc_fk_display_columns')
                ->nullable()
                ->comment('Kolom yang ditampilkan: ["kategori_kode", "kategori_nama"]');
            
            // Field Properties
            $table->integer('wmfc_order')
                ->default(0)
                ->comment('Urutan field di form (ascending)');
            
            $table->tinyInteger('wmfc_is_primary_key')
                ->default(0)
                ->comment('Apakah kolom ini Primary Key (0=tidak, 1=ya)');
            
            $table->tinyInteger('wmfc_is_auto_increment')
                ->default(0)
                ->comment('Apakah PK auto increment - hidden di form (0=tidak, 1=ya)');
            
            $table->tinyInteger('wmfc_is_visible')
                ->default(1)
                ->comment('Tampilkan di form atau tidak (0=hidden, 1=visible)');
            
            // Common Fields (Soft Delete Support)
            $table->tinyInteger('isDeleted')
                ->default(0)
                ->comment('Status soft delete (0=active, 1=deleted)');
            
            $table->char('created_by', 36)
                ->nullable()
                ->comment('UUID user yang membuat record');
            
            $table->timestamp('created_at')
                ->useCurrent()
                ->comment('Waktu record dibuat');
            
            $table->char('updated_by', 36)
                ->nullable()
                ->comment('UUID user yang terakhir update record');
            
            $table->timestamp('updated_at')
                ->useCurrent()
                ->useCurrentOnUpdate()
                ->comment('Waktu record terakhir diupdate');
            
            $table->char('deleted_by', 36)
                ->nullable()
                ->comment('UUID user yang menghapus record');
            
            $table->timestamp('deleted_at')
                ->nullable()
                ->comment('Waktu record dihapus (soft delete)');
            
            // Indexes untuk performa query
            $table->index('fk_web_menu_url', 'idx_wmfc_menu_url');
            $table->index('wmfc_column_name', 'idx_wmfc_column_name');
            $table->index('wmfc_order', 'idx_wmfc_order');
            $table->index('wmfc_is_visible', 'idx_wmfc_visible');
            $table->index('isDeleted', 'idx_wmfc_isdeleted');
            
            // Foreign Key Constraint
            $table->foreign('fk_web_menu_url', 'fk_wmfc_web_menu_url')
                ->references('web_menu_url_id')
                ->on('web_menu_url')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drop tabel web_menu_field_config
     */
    public function down(): void
    {
        Schema::dropIfExists('web_menu_field_config');
    }
};
