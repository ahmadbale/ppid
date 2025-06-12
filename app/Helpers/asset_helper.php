<?php
// filepath: d:\laragon\www\PPID-polinema\app\Helpers\asset_helper.php

if (!function_exists('sisfo_asset')) {
    function sisfo_asset($path) {
        $assetUrl = env('ASSET_URL_SISFO', config('app.url'));
        return $assetUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('user_asset')) {
    function user_asset($path) {
        $assetUrl = env('ASSET_URL_USER', config('app.url'));
        return $assetUrl . '/' . ltrim($path, '/');
    }
}

if (!function_exists('image_asset')) {
    function image_asset($path)
    {
        return env('IMAGE_ASSET', url('storage')) . '/' . ltrim($path, '/');
    }
}