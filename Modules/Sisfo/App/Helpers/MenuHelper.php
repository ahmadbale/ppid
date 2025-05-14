<?php

namespace Modules\Sisfo\App\Helpers;

use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Illuminate\Support\Facades\Auth;

class MenuHelper
{
    public static function renderSidebarMenus($hakAksesKode, $activeMenu)
    {
        if (empty($hakAksesKode)) {
            return ''; // Jika kode hak akses kosong, tidak ada menu yang ditampilkan
        }

        $userId = Auth::user()->user_id;
        $menus = WebMenuModel::getMenusByLevelWithPermissions($hakAksesKode, $userId);
        $totalNotifikasi = WebMenuModel::getNotifikasiCount($hakAksesKode);

        $menuIcons = [
            'Dashboard' => 'fa-tachometer-alt',
            'Profile' => 'fa-user',
            'Notifikasi' => 'fa-bell',
            'Hak Akses' => 'fa-key',
            'E-Form Admin' => 'fa-folder-open',
            'Menu Management' => 'fa-tasks',
            'Management Pengumuman' => 'fa-bullhorn',
            'Management Berita' => 'fa-newspaper',
            'Management Footer' => 'fa-columns',
            'Management LHKPN' => 'fa-file-alt',
            'Management Akses & Pintasan Cepat' => 'fa-bolt',
            'Management Form' => 'fa-question-circle',
            'Management Regulasi' => 'fa-gavel',
            'Management Pengguna' => 'fa-user-cog',
            'Management Media' => 'fa-photo-video',
        ];

        $html = '';

        // Dashboard dan Profil selalu ada
        $html .= self::generateMenuItem(
            url('/dashboard' . strtoupper($hakAksesKode)),
            'Dashboard',
            $menuIcons['Dashboard'],
            $activeMenu
        );

        $html .= self::generateMenuItem(
            url('/profile'),
            'Profile',
            $menuIcons['Profile'],
            $activeMenu
        );

        // Notifikasi untuk level tertentu
        if (in_array($hakAksesKode, ['ADM', 'VFR', 'MPU'])) {
            $notifUrl = [
                'ADM' => '/Notifikasi/NotifAdmin',
                'VFR' => '/notifikasi',
                'MPU' => '/notifMPU'
            ][$hakAksesKode];

            $html .= self::generateNotificationMenuItem(
                url($notifUrl),
                'Notifikasi',
                $menuIcons['Notifikasi'],
                $activeMenu,
                $totalNotifikasi
            );
        }

        // Menu dinamis dari database
        foreach ($menus as $menu) {
            // Ambil nama menu yang akan ditampilkan (bisa alias atau nama asli)
            $menuName = $menu->getDisplayName();

            if ($menu->children->isNotEmpty()) {
                // Menu dengan submenu
                $html .= self::generateDropdownMenu($menu, $activeMenu, $menuIcons);
            } else {
                // Menu tanpa submenu - Gunakan URL yang sesuai
                $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : '#';
                $html .= self::generateMenuItem(
                    url($menuUrl),
                    $menuName,
                    $menuIcons[$menuName] ?? 'fa-tasks',
                    $activeMenu
                );
            }
        }

        // Menu khusus SAR
        if ($hakAksesKode == 'SAR') {
            $html .= self::generateMenuItem(
                url('/HakAkses'),
                'Pengaturan Hak Akses',
                $menuIcons['Hak Akses'],
                $activeMenu
            );
        }

        return $html;
    }

    private static function generateMenuItem($url, $name, $icon, $activeMenu)
    {
        // Standardisasi format nama menu untuk pemeriksaan active state
        $menuSlug = strtolower(str_replace(' ', '', $name));
        $isActive = ($activeMenu == $menuSlug) ? 'active' : '';
        return "
    <li class='nav-item'>
        <a href='{$url}' class='nav-link {$isActive}'>
            <i class='nav-icon fas {$icon}'></i>
            <p>{$name}</p>
        </a>
    </li>";
    }

    private static function generateNotificationMenuItem($url, $name, $icon, $activeMenu, $notificationCount)
    {
        $isActive = ($activeMenu == 'notifikasi') ? 'active' : '';
        $notificationBadge = $notificationCount > 0
            ? "<span class='badge badge-danger notification-badge'>{$notificationCount}</span>"
            : '';

        return "
        <li class='nav-item'>
            <a href='{$url}' class='nav-link {$isActive}'>
                <i class='nav-icon fas {$icon}'></i>
                <p>{$name} {$notificationBadge}</p>
            </a>
        </li>";
    }

    private static function generateDropdownMenu($menu, $activeMenu, $menuIcons)
    {
        // Ambil nama menu yang akan ditampilkan
        $menuName = $menu->getDisplayName();

        // Standardisasi format nama menu
        $menuSlug = strtolower(str_replace(' ', '', $menuName));

        // Tentukan icon untuk parent menu
        $parentIcon = isset($menuIcons[$menuName]) ? $menuIcons[$menuName] : 'fa-cog';

        // Periksa apakah ada submenu yang aktif
        $hasActiveSubmenu = false;
        foreach ($menu->children as $submenu) {
            $submenuName = $submenu->getDisplayName();
            $submenuSlug = strtolower(str_replace(' ', '', $submenuName));
            if ($activeMenu == $submenuSlug) {
                $hasActiveSubmenu = true;
                break;
            }
        }

        // Tambahkan class menu-open jika ada submenu yang aktif
        $menuOpen = $hasActiveSubmenu ? 'menu-open' : '';
        $parentActive = $hasActiveSubmenu ? 'active' : '';

        $html = "
            <li class='nav-item {$menuOpen}'>
                <a href='#' class='nav-link {$parentActive}'>
                    <i class='nav-icon fas {$parentIcon}'></i>
                    <p>{$menuName}
                        <i class='right fas fa-angle-left'></i>
                    </p>
                </a>
                <ul class='nav nav-treeview'>";

        foreach ($menu->children as $submenu) {
            // Ambil nama submenu yang akan ditampilkan
            $submenuName = $submenu->getDisplayName();

            // Standardisasi format submenu untuk pemeriksaan active state
            $submenuSlug = strtolower(str_replace(' ', '', $submenuName));

            $submenuUrl = $submenu->WebMenuUrl ? $submenu->WebMenuUrl->wmu_nama : '#';
            $isActive = ($activeMenu == $submenuSlug) ? 'active' : '';

            $html .= "
            <li class='nav-item'>
                <a href='" . url($submenuUrl) . "' class='nav-link {$isActive}'>
                    <i class='far fa-circle nav-icon'></i>
                    <p>{$submenuName}</p>
                </a>
            </li>";
        }

        $html .= "
            </ul>
        </li>";

        return $html;
    }
}
