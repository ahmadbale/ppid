<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    // Entry point utama - resolve URL ke controller dari database, map HTTP method ke action, lalu call controller method yang sesuai
    public function index(Request $request, string $page, ?string $action = null, ?string $id = null)
    {
        try {
            // STEP 1: Get Menu URL dari Database
            $menuUrl = $this->getMenuUrl($page);
            
            if (!$menuUrl) {
                return $this->handleNotFound($page, "Menu '{$page}' tidak ditemukan");
            }
            
            if (!$menuUrl->controller_name) {
                return $this->handleNotFound($page, "Controller untuk menu '{$page}' belum dikonfigurasi");
            }
            
            // STEP 2: Resolve Action berdasarkan HTTP method & path
            $finalAction = $this->resolveAction($request, $action);
            
            // STEP 3: Resolve Controller Class
            $controllerClass = $this->resolveControllerClass($menuUrl->controller_name);
            
            if (!class_exists($controllerClass)) {
                return $this->handleControllerNotFound($menuUrl->controller_name, $controllerClass);
            }
            
            // STEP 4: Create Controller Instance
            $controller = app($controllerClass);
            
            // STEP 5: Check Method Exists
            if (!method_exists($controller, $finalAction)) {
                return $this->handleMethodNotFound($menuUrl->controller_name, $finalAction);
            }
            
            // STEP 6: Prepare Parameters
            $params = $this->prepareParameters($request, $finalAction, $id);
            
            // STEP 7: Log Request (for debugging)
            $this->logRequest($page, $finalAction, $id, $request->method());
            
            // STEP 8: Call Controller Method
            return $controller->$finalAction(...$params);
            
        } catch (\Exception $e) {
            return $this->handleException($e, $page, $action, $id);
        }
    }
    
    // Query database web_menu_url untuk ambil controller name berdasarkan URL page
    // HANYA ambil URL dengan module_type = 'sisfo' (skip URL module 'user')
    private function getMenuUrl(string $page): ?object
    {
        return DB::table('web_menu_url')
            ->where('wmu_nama', $page)
            ->where('module_type', 'sisfo')  // ← FILTER: Hanya Sisfo URLs
            ->where('isDeleted', 0)
            ->select('web_menu_url_id', 'wmu_nama', 'controller_name', 'module_type')
            ->first();
    }
    
    // Map HTTP method (GET/POST/DELETE) dan action dari URL ke method name controller - contoh: POST /{page} → createData()
    private function resolveAction(Request $request, ?string $action): string
    {
        // Jika tidak ada action parameter → default routing
        if ($action === null) {
            if ($request->isMethod('POST')) {
                return 'createData';
            }
            return 'index';
        }
        
        // Special handling untuk updateData
        if ($action === 'updateData') {
            // updateData bisa dipanggil dengan POST atau PUT
            // Controller method tetap sama: updateData()
            return 'updateData';
        }
        
        // deleteData support GET (confirm page) & DELETE (process)
        // Controller method handle sendiri perbedaan GET vs DELETE
        if ($action === 'deleteData') {
            return 'deleteData';
        }
        
        // Action lainnya langsung pass-through
        // getData, addData, editData, detailData, dll
        return $action;
    }
    
    // Build full namespace controller class dari controller name di database - contoh: AdminWeb\Footer\FooterController → Modules\Sisfo\App\Http\Controllers\AdminWeb\Footer\FooterController
    private function resolveControllerClass(string $controllerName): string
    {
        // Controller name dari database sudah relative path
        // Contoh: AdminWeb\Footer\FooterController
        // Kita tambahkan base namespace
        return "Modules\\Sisfo\\App\\Http\\Controllers\\" . $controllerName;
    }
    
    // Siapkan array parameter untuk call method - method dengan ID (editData, deleteData) butuh parameter [$id], method lain kosong []
    private function prepareParameters(Request $request, string $action, ?string $id): array
    {
        // Method yang butuh Request dan ID: updateData($request, $id), deleteData($request, $id)
        $methodsWithRequestAndId = [
            'updateData',
            'deleteData'
        ];
        
        // Jika method butuh Request + ID
        if (in_array($action, $methodsWithRequestAndId) && $id !== null) {
            return [$request, $id];
        }
        
        // Method yang hanya butuh ID parameter: editData($id), detailData($id)
        $methodsWithId = [
            'editData',
            'detailData',
            'removeImage',
            'removeHakAkses'
        ];
        
        // Jika method hanya butuh ID
        if (in_array($action, $methodsWithId) && $id !== null) {
            return [$id];
        }
        
        // Method yang butuh Request di parameter pertama: index($request), getData($request), dll
        $methodsWithRequest = [
            'index',
            'getData',
            'addData',
            'createData'
        ];
        
        // Jika method butuh Request
        if (in_array($action, $methodsWithRequest)) {
            return [$request];
        }
        
        // Method lain tidak perlu parameter
        return [];
    }
    
    // Log request untuk debugging di local environment - format: "PageController: GET /kategori-footer/editData/123"
    private function logRequest(string $page, string $action, ?string $id, string $method): void
    {
        // Log hanya di development/local environment
        if (config('app.env') === 'local' || config('app.debug')) {
            $idPart = $id ? "/{$id}" : '';
            Log::debug("PageController: {$method} /{$page}/{$action}{$idPart}");
        }
    }
    
    // Handle error 404 ketika menu tidak ditemukan di database - return JSON atau abort(404) tergantung request type
    private function handleNotFound(string $page, string $message): mixed
    {
        Log::warning("PageController 404: {$message}", [
            'page' => $page,
            'url' => request()->fullUrl()
        ]);
        
        // Return view error atau abort 404
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message
            ], 404);
        }
        
        abort(404, $message);
    }
    
    // Handle error ketika controller class tidak ditemukan - log error dan return response sesuai request type
    private function handleControllerNotFound(string $controllerName, string $fullClass): mixed
    {
        $message = "Controller '{$controllerName}' tidak ditemukan";
        
        Log::error("PageController: Controller not found", [
            'controller_name' => $controllerName,
            'full_class' => $fullClass,
            'url' => request()->fullUrl()
        ]);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'debug' => config('app.debug') ? [
                    'controller_name' => $controllerName,
                    'full_class' => $fullClass
                ] : null
            ], 500);
        }
        
        abort(500, $message);
    }
    
    // Handle error ketika method tidak ditemukan di controller - misal controller ada tapi method getData() belum dibuat
    private function handleMethodNotFound(string $controllerName, string $method): mixed
    {
        $message = "Method '{$method}()' tidak ditemukan di controller '{$controllerName}'";
        
        Log::error("PageController: Method not found", [
            'controller' => $controllerName,
            'method' => $method,
            'url' => request()->fullUrl()
        ]);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'debug' => config('app.debug') ? [
                    'controller' => $controllerName,
                    'method' => $method,
                    'available_methods' => '8 standard methods: index, getData, addData, createData, editData, updateData, detailData, deleteData'
                ] : null
            ], 500);
        }
        
        abort(500, $message);
    }
    
    // Handle general exception - catch semua error yang tidak tertangani, log detail error, dan return response aman ke user
    private function handleException(\Exception $e, string $page, ?string $action, ?string $id): mixed
    {
        Log::error("PageController Exception", [
            'page' => $page,
            'action' => $action,
            'id' => $id,
            'exception' => $e->getMessage(),
            'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            'url' => request()->fullUrl()
        ]);
        
        if (request()->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
        
        // Re-throw jika bukan production
        if (config('app.debug')) {
            throw $e;
        }
        
        abort(500, 'Terjadi kesalahan sistem');
    }
}
