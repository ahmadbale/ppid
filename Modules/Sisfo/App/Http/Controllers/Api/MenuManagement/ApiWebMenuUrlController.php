<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Models\ApplicationModel;

class ApiWebMenuUrlController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($request) {
                $appKey = $request->query('app_key', 'app ppid');
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);

                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $data = WebMenuUrlModel::selectData($perPage, $search, $appKey);
                
                return [
                    'webMenuUrls' => $data,
                    'app_key' => $appKey,
                    'available_apps' => WebMenuUrlModel::getApplications(),
                    'breadcrumb' => [
                        'title' => 'Manajemen URL Menu',
                        'list' => ['Home', 'Manajemen URL Menu']
                    ],
                    'page' => [
                        'title' => 'Daftar URL Menu'
                    ]
                ];
            },
            'web menu url',
            self::ACTION_GET
        );
    }

    
    public function getData(Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                $appKey = $request->query('app_key', 'app ppid');

                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $webMenuUrls = WebMenuUrlModel::selectData($perPage, $search, $appKey);
                
                return [
                    'webMenuUrls' => $webMenuUrls,
                    'search' => $search,
                    'app_key' => $appKey
                ];
            },
            'web menu url data',
            self::ACTION_GET
        );
    }

    // ✅ TAMBAHAN: Method addData untuk form data (sesuai web route)
    public function addData(Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($request) {
                $appKey = $request->query('app_key', 'app ppid');

                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $applications = ApplicationModel::where('isDeleted', 0)->get();
                
                return [
                    'applications' => $applications,
                    'app_key' => $appKey,
                    'form_action' => 'create'
                ];
            },
            'add web menu url form',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                $appKey = $request->query('app_key');
                if ($appKey && !WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                WebMenuUrlModel::validasiData($request);
                $result = WebMenuUrlModel::createData($request);
                
                
                return $result['data'] ?? null;
            },
            'web menu url',
            self::ACTION_CREATE
        );
    }

    
    public function editData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($id, $request) {
                $appKey = $request->query('app_key', 'app ppid');

                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $webMenuUrl = WebMenuUrlModel::detailData($id);
                $applications = ApplicationModel::where('isDeleted', 0)->get();
                
                return [
                    'webMenuUrl' => $webMenuUrl,
                    'applications' => $applications,
                    'app_key' => $appKey,
                    'form_action' => 'update'
                ];
            },
            'edit web menu url',
            self::ACTION_GET
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request, $id) {
                $appKey = $request->query('app_key');
                if ($appKey && !WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                WebMenuUrlModel::validasiData($request);
                $result = WebMenuUrlModel::updateData($request, $id);
                
            
                return $result['data'] ?? null;
            },
            'web menu url',
            self::ACTION_UPDATE
        );
    }


    public function deleteData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($id, $request) {
                try {
                    $result = WebMenuUrlModel::deleteData($id);
                    
                 
                    if (!$result['success']) {
                        // Jika gagal karena masih digunakan
                        if (isset($result['error_code']) && $result['error_code'] === 'URL_IN_USE') {
                            return $this->errorResponse(
                                'URL_IN_USE',
                                $result['message'],
                                self::HTTP_BAD_REQUEST,  // 400 Bad Request (bukan 404)
                                $result['usage_details'] ?? null
                            );
                        }
                        
                        // Jika gagal karena data tidak ditemukan
                        if (isset($result['error_code']) && $result['error_code'] === 'DATA_NOT_FOUND') {
                            return $this->errorResponse(
                                'DATA_NOT_FOUND',
                                $result['message'],
                                self::HTTP_NOT_FOUND    // 404 Not Found
                            );
                        }
                        

                        return $this->errorResponse(
                            'DELETE_FAILED',
                            $result['message'],
                            self::HTTP_BAD_REQUEST
                        );
                    }
                    
                    // SUCCESS response
                    return [
                        'message' => $result['message'],
                        'deleted_item' => [
                            'web_menu_url_id' => $result['data']->web_menu_url_id,
                            'wmu_nama' => $result['data']->wmu_nama,
                            'deleted_at' => $result['data']->deleted_at
                        ]
                    ];
                    
                } catch (\Exception $e) {
                    return $this->errorResponse(
                        'DELETE_ERROR',
                        'Terjadi kesalahan saat menghapus: ' . $e->getMessage(),
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }
            },
            'web menu url',
            self::ACTION_DELETE
        );
    }

    public function detailData($id, Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($id, $request) {
                $appKey = $request->query('app_key', 'app ppid');

                if (!WebMenuUrlModel::validateAppKey($appKey)) {
                    return $this->errorResponse(
                        'APP_KEY_INVALID',
                        "App key '{$appKey}' tidak ditemukan dalam sistem",
                        self::HTTP_BAD_REQUEST
                    );
                }

                $data = WebMenuUrlModel::detailData($id);
                
                return [
                    'webMenuUrl' => $data,
                    'app_key' => $appKey,
                    'title' => 'Detail URL Menu'
                ];
            },
            'web menu url detail',
            self::ACTION_GET
        );
    }

    // Method untuk mendapatkan daftar aplikasi
    public function getApplications(Request $request)
    {
        return $this->executeWithAuthentication(
            function($user) use ($request) {
                $applications = ApplicationModel::where('isDeleted', 0)->get();
                
                return [
                    'applications' => $applications
                ];
            },
            'applications',
            self::ACTION_GET
        );
    }
}