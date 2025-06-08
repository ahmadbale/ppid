<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WhatsAppService;

class TestWhatsAppCommand extends Command
{
    protected $signature = 'whatsapp:test {nomor} {pesan?}';
    protected $description = 'Test WhatsApp service';

    public function handle()
    {
        $nomor = $this->argument('nomor');
        $pesan = $this->argument('pesan') ?? 'Test pesan dari PPID Polinema - ' . date('d/m/Y H:i:s');

        $this->info("🚀 Testing WhatsApp Service...");
        $this->info("📱 Nomor tujuan: {$nomor}");
        $this->info("💬 Pesan: {$pesan}");
        $this->line('');

        $whatsappService = new WhatsAppService();

        // Cek status server
        $this->info("🔍 Mengecek status WhatsApp server...");
        if (!$whatsappService->cekStatus()) {
            $this->error('❌ WhatsApp server tidak tersedia!');
            $this->error('💡 Pastikan server WhatsApp berjalan di: http://localhost:3000');
            return self::FAILURE;
        }

        $this->info("✅ WhatsApp server tersedia");
        $this->line('');

        // Kirim pesan test
        $this->info("📤 Mengirim pesan test...");
        $berhasil = $whatsappService->kirimPesan($nomor, $pesan, 'Test');

        if ($berhasil) {
            $this->info('✅ Pesan berhasil dikirim!');
            $this->info('📋 Cek log WhatsApp di database untuk detail');
            return self::SUCCESS;
        } else {
            $this->error('❌ Gagal mengirim pesan!');
            $this->error('📋 Cek log Laravel untuk detail error');
            return self::FAILURE;
        }
    }
}