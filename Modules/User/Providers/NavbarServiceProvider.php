<?php

namespace Modules\User\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class NavbarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Log::info('NavbarServiceProvider: Boot method dipanggil');
        View::composer('user::layouts.navbar', function ($view) {
            try {
                Log::info('Memulai proses pengambilan data navbar');

                $navbarData = $this->getNavbarData();
                Log::info('Data navbar berhasil diperoleh', ['navbarData' => $navbarData]);

                $view->with('navbar', $navbarData);
            } catch (\Exception $e) {
                Log::error('Error in NavbarServiceProvider', ['message' => $e->getMessage()]);
                $view->with('navbar', $this->getDefaultNavbarData());
            }
        });
    }

    private function getNavbarData()
    {
        try {
            Log::info('Mengambil data navbar dari API publik');
            $response = Http::get('http://ppid-polinema.test/api/public/getDataMenu');

            if ($response->failed() || !$response->json('success')) {
                Log::warning('Navbar API gagal diambil atau data tidak lengkap', [
                    'response' => $response->json() ?? 'Tidak ada response'
                ]);
                return $this->getDefaultNavbarData();
            }

            Log::info('NavbarServiceProvider: Response API', ['response' => $response->json()]);
            return $this->processNavbarData($response->json('data'));
        } catch (\Exception $e) {
            Log::error('Navbar Data Fetch Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getDefaultNavbarData();
        }
    }

    private function processNavbarData($data)
    {
        $menuTree = [];
        $menuMap = [];

        // Buat peta menu berdasarkan ID
        foreach ($data as $item) {
            $menuMap[$item['id']] = array_merge($item, ['children' => []]);
        }

        // Susun menu berdasarkan parent_id
        foreach ($menuMap as $id => &$menuItem) {
            if ($menuItem['wm_parent_id']) {
                $menuMap[$menuItem['wm_parent_id']]['children'][] = &$menuItem;
            } else {
                $menuTree[] = &$menuItem;
            }
        }

        return $menuTree;
    }

    private function getDefaultNavbarData()
    {
        return [];
    }
}
