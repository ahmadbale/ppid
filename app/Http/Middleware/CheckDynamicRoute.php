<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\RouteHelper;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckDynamicRoute
{
    // Validasi apakah URL boleh masuk dynamic routing atau harus di-handle route khusus
    public function handle(Request $request, Closure $next): Response
    {
        $page = $request->route('page');
        
        if (!$page) {
            return $next($request);
        }
        
        if (!RouteHelper::isDynamicRoutingUrl($page)) {
            if (config('app.debug')) {
                Log::debug("CheckDynamicRoute: URL '{$page}' excluded", [
                    'url' => $request->fullUrl(),
                    'is_user_module' => RouteHelper::isUserModuleUrl($page),
                    'is_non_standard' => RouteHelper::isNonStandardSisfoUrl($page),
                ]);
            }
            
            abort(404, "URL '{$page}' tidak ditemukan");
        }
        
        if (config('app.debug')) {
            Log::debug("CheckDynamicRoute: URL '{$page}' allowed", [
                'url' => $request->fullUrl(),
            ]);
        }
        
        return $next($request);
    }
}

