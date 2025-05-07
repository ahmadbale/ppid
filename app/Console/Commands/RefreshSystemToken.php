<?php

namespace App\Console\Commands;

use App\Services\JwtTokenService;
use Illuminate\Console\Command;

class RefreshSystemToken extends Command
{
    protected $signature = 'system:refresh-token';
    protected $description = 'Refresh sistem API token jika mendekati masa expired';

    protected $jwtTokenService;

    public function __construct(JwtTokenService $jwtTokenService)
    {
        parent::__construct();
        $this->jwtTokenService = $jwtTokenService;
    }

    public function handle()
    {
        $this->info('Memeriksa dan memperbarui token sistem...');
        
        try {
            // Periksa apakah token mendekati expired
            if ($this->jwtTokenService->isTokenNearExpiration()) {
                // Generate token baru
                $tokenData = $this->jwtTokenService->generateSystemToken();
                
                $this->info('Token berhasil diperbarui.');
                $this->info('Token baru akan expired pada: ' . $tokenData['expires_at']);
            } else {
                $this->info('Token masih valid, tidak perlu diperbarui.');
            }
        } catch (\Exception $e) {
            $this->error('Gagal memperbarui token: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}