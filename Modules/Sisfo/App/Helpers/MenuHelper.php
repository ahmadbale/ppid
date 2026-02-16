<?php

namespace Modules\Sisfo\App\Helpers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Sisfo\App\Models\HakAksesModel;
use Modules\Sisfo\App\Models\Website\WebMenuModel;
use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;

class MenuHelper
{
    public static function renderSidebarMenus($hakAksesKode, $activeMenu)
    {
        if (empty($hakAksesKode)) {
            return '';
        }

        $userId = Auth::user()->user_id;
        $menus = WebMenuModel::getMenusByLevelWithPermissions($hakAksesKode, $userId);

        $html = '';

        $html .= self::generateMenuItem(
            url('/dashboard' . strtoupper($hakAksesKode)),
            'Dashboard',
            'fa-tachometer-alt',
            $activeMenu
        );

        $html .= self::generateMenuItem(
            url('/profile'),
            'Profile',
            'fa-user',
            $activeMenu
        );

        // Menu dinamis dari database
        foreach ($menus as $menu) {
            $menuName = $menu->getDisplayName();
            $menuIcon = $menu->WebMenuGlobal->wmg_icon ?? 'fa-cog';
            $badgeCount = self::getBadgeCount($menu);

            if ($menu->children->isNotEmpty()) {
                $html .= self::generateDropdownMenu($menu, $activeMenu);
            } else {
                $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : '#';
                $html .= self::generateMenuItem(
                    url($menuUrl),
                    $menuName,
                    $menuIcon,
                    $activeMenu,
                    $badgeCount
                );
            }
        }

        // Menu khusus SAR
        if ($hakAksesKode == 'SAR') {
            $html .= self::generateMenuItem(
                url('/HakAkses'),
                'Pengaturan Hak Akses',
                'fa-key',
                $activeMenu
            );
        }

        return $html;
    }

    private static function generateMenuItem($url, $name, $icon, $activeMenu, $badgeCount = 0)
    {
        $menuSlug = strtolower(str_replace(' ', '', $name));
        $isActive = ($activeMenu == $menuSlug) ? 'active' : '';
        
        $badge = '';
        if ($badgeCount > 0) {
            $badge = "<span class='badge badge-danger notification-badge'>{$badgeCount}</span>";
        }
        
        return "
    <li class='nav-item'>
        <a href='{$url}' class='nav-link {$isActive}'>
            <i class='nav-icon fas {$icon}'></i>
            <p>{$name} {$badge}</p>
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

    private static function generateDropdownMenu($menu, $activeMenu)
    {
        // Ambil nama menu yang akan ditampilkan
        $menuName = $menu->getDisplayName();

        // Standardisasi format nama menu
        $menuSlug = strtolower(str_replace(' ', '', $menuName));

        // Get icon dari web_menu_global, default 'fa-cog' jika NULL
        $parentIcon = $menu->WebMenuGlobal->wmg_icon ?? 'fa-cog';

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
            $submenuName = $submenu->getDisplayName();
            $submenuSlug = strtolower(str_replace(' ', '', $submenuName));
            $submenuUrl = $submenu->WebMenuUrl ? $submenu->WebMenuUrl->wmu_nama : '#';
            $isActive = ($activeMenu == $submenuSlug) ? 'active' : '';
            $submenuIcon = $submenu->WebMenuGlobal->wmg_icon ?? 'fa-circle';
            $badgeCount = self::getBadgeCount($submenu);

            $badge = '';
            if ($badgeCount > 0) {
                $badge = "<span class='badge badge-danger ml-auto'>{$badgeCount}</span>";
            }

            $html .= "
            <li class='nav-item'>
                <a href='" . url($submenuUrl) . "' class='nav-link {$isActive}'>
                    <i class='far {$submenuIcon} nav-icon'></i>
                    <p>{$submenuName} {$badge}</p>
                </a>
            </li>";
        }

        $html .= "
            </ul>
        </li>";

        return $html;
    }

    private static function getBadgeCount($menu)
    {
        if (!$menu->WebMenuGlobal || !$menu->WebMenuGlobal->wmg_badge_method || !$menu->WebMenuUrl || !$menu->WebMenuUrl->controller_name) {
            return 0;
        }

        $badgeMethod = $menu->WebMenuGlobal->wmg_badge_method;
        $controllerName = $menu->WebMenuUrl->controller_name;
        $moduleType = $menu->WebMenuUrl->module_type ?? 'sisfo';

        $moduleNamespace = $moduleType === 'sisfo' ? 'Sisfo' : 'User';
        $controllerClass = "Modules\\{$moduleNamespace}\\App\\Http\\Controllers\\" . str_replace('/', '\\', $controllerName);

        if (!class_exists($controllerClass)) {
            return 0;
        }

        try {
            $controller = app($controllerClass);
            
            if (method_exists($controller, $badgeMethod)) {
                return $controller->{$badgeMethod}() ?? 0;
            }
        } catch (\Exception $e) {
            Log::warning("Failed to get badge count for menu: " . $menu->wm_menu_nama, [
                'controller' => $controllerClass,
                'method' => $badgeMethod,
                'error' => $e->getMessage()
            ]);
        }

        return 0;
    }

    public static function getSpecialMenusByLevel($hakAksesKode, $userId)
    {
        $level = HakAksesModel::where('hak_akses_kode', $hakAksesKode)->first();
        if (!$level) return collect([]);

        $hakAksesId = $level->hak_akses_id;

        $hasLevel = DB::table('set_user_hak_akses')
            ->where('fk_m_user', $userId)
            ->where('fk_m_hak_akses', $hakAksesId)
            ->where('isDeleted', 0)
            ->exists();

        if (!$hasLevel && $hakAksesKode !== 'SAR') {
            return collect([]);
        }

        $menus = WebMenuModel::where('fk_m_hak_akses', $hakAksesId)
            ->where('wm_status_menu', 'aktif')
            ->where('isDeleted', 0)
            ->whereHas('WebMenuGlobal', function ($query) {
                $query->where('wmg_type', 'special');
            })
            ->with(['WebMenuGlobal.WebMenuUrl', 'Level'])
            ->orderBy('wm_urutan_menu')
            ->get();

        $filteredMenus = $menus->filter(function ($menu) use ($userId) {
            if ($menu->WebMenuUrl) {
                return SetHakAksesModel::cekHakAksesMenu($userId, $menu->WebMenuUrl->wmu_nama);
            }
            return false;
        });

        return $filteredMenus;
    }

    public static function renderHeaderMenus($hakAksesKode, $userId)
    {
        $menus = self::getSpecialMenusByLevel($hakAksesKode, $userId);
        $html = '';

        foreach ($menus as $menu) {
            $menuIcon = $menu->WebMenuGlobal->wmg_icon ?? 'fa-bell';
            $menuUrl = $menu->WebMenuUrl ? $menu->WebMenuUrl->wmu_nama : '#';
            $menuCategory = $menu->WebMenuGlobal->wmg_kategori_menu ?? '';
            $badgeCount = self::getBadgeCount($menu);

            $badge = '';
            if ($badgeCount > 0) {
                $badge = '<span class="badge badge-danger navbar-badge" style="font-size: 12px; top: 0; right: 0;">' . $badgeCount . '</span>';
            }

            if ($menuCategory === 'notifikasi') {
                $html .= '<li class="nav-item dropdown d-flex align-items-center mr-3">
                    <a href="' . url($menuUrl) . '" class="nav-link d-flex align-items-center" style="font-size: 1.3rem;">
                        <i class="far ' . $menuIcon . ' nav-icon"></i>
                        ' . $badge . '
                    </a>
                </li>';
            } else {
                $html .= '<li class="nav-item d-flex align-items-center mr-3">
                    <a href="' . url($menuUrl) . '" class="nav-link d-flex align-items-center" style="font-size: 1.3rem;">
                        <i class="far ' . $menuIcon . ' nav-icon"></i>
                        ' . $badge . '
                    </a>
                </li>';
            }
        }

        return $html;
    }
}
