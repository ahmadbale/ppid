<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\MenuManagement;

use Illuminate\Http\Request;
use Modules\Sisfo\App\Models\WebMenuGlobalModel;
use Modules\Sisfo\App\Models\Website\WebMenuUrlModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiWebMenuGlobalController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function () use ($request) {
                $search = $request->query('search', '');
                $perPage = $request->query('per_page', 10);
                return WebMenuGlobalModel::selectData($perPage, $search);
            },
            'menu global',
            self::ACTION_GET
        );
    }

    public function createData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request) {
                // Ambil data dari request, baik JSON maupun form-data
                $wmgNamaDefault = $request->input('wmg_nama_default') ?? $request->json('wmg_nama_default');
                // fk_web_menu_url bisa null (group menu)
                $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $request->json('fk_web_menu_url');

                // Validasi data dasar
                if (empty($wmgNamaDefault) && $wmgNamaDefault !== '0') {
                    return $this->errorResponse(
                        self::AUTH_INVALID_INPUT,
                        'Nama default menu (wmg_nama_default) harus diisi',
                        422
                    );
                }

                // Struktur ulang data
                $request->merge([
                    'web_menu_global' => [
                        'wmg_nama_default' => $wmgNamaDefault,
                        // Jika dikirim string kosong atau "null", simpan sebagai null
                        'fk_web_menu_url' => ($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl
                    ]
                ]);

                // Validasi data menggunakan model
                WebMenuGlobalModel::validasiData($request);

                // Buat data baru
                $result = WebMenuGlobalModel::createData($request);
                return $result['data'];
            },
            'menu global',
            self::ACTION_CREATE
        );
    }

    public function updateData(Request $request, $id)
    {
        return $this->executeWithAuthAndValidation(
            function ($user) use ($request, $id) {
                if ($request->has('wmg_nama_default')) {
                    $fkWebMenuUrl = $request->input('fk_web_menu_url') ?? $request->json('fk_web_menu_url');
                    $request->merge([
                        'web_menu_global' => [
                            'wmg_nama_default' => $request->input('wmg_nama_default'),
                            'fk_web_menu_url' => ($fkWebMenuUrl === '' || $fkWebMenuUrl === 'null') ? null : $fkWebMenuUrl
                        ]
                    ]);
                } elseif (!$request->has('web_menu_global')) {
                    return $this->errorResponse(
                        self::INVALID_REQUEST_FORMAT . '. Harap sertakan wmg_nama_default',
                        null,
                        422
                    );
                }

                WebMenuGlobalModel::validasiData($request);

                $result = WebMenuGlobalModel::updateData($request, $id);

                return $result['data'] ?? null;
            },
            'menu global',
            self::ACTION_UPDATE
        );
    }

    public function deleteData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                $result = WebMenuGlobalModel::deleteData($id);
                return $result['data'];
            },
            'menu global',
            self::ACTION_DELETE
        );
    }

    public function detailData($id)
    {
        return $this->executeWithAuthentication(
            function () use ($id) {
                return WebMenuGlobalModel::detailData($id);
            },
            'menu global',
            self::ACTION_GET
        );
    }

    public function getMenuUrl                                                                              ()
    {
        return $this->executeWithAuthentication(
            function () {
                $menuUrls = WebMenuUrlModel::with('application')->where('isDeleted', 0)->get();
                return $menuUrls;
            },
            'menu global',
            self::ACTION_GET
        );
    }
}