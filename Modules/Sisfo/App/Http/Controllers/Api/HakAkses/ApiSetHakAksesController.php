<?php

namespace Modules\Sisfo\App\Http\Controllers\Api\HakAkses;

use Illuminate\Http\Request;

use Modules\Sisfo\App\Models\HakAkses\SetHakAksesModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class APISetHakAksesController extends BaseApiController
{

    public function index()
    {
        return $this->executeWithAuthentication(
            function() {
                $result = SetHakAksesModel::selectData();
                return $result['data']; // mengembalikan leveluser
            },
            'hak akses',
            self::ACTION_GET
        );
    }

    public function editData($param1, $param2 = null)
    {
        return $this->executeWithAuthentication(
            function() use ($param1, $param2) {
                // Jika param2 tidak ada, ini adalah permintaan hak akses berdasarkan level
                if ($param2 === null) {
                    $hak_akses_kode = $param1;
                    return SetHakAksesModel::getHakAksesData($hak_akses_kode);
                }
                // Jika param2 ada, ini adalah permintaan hak akses spesifik user dan menu
                else {
                    $pengakses_id = $param1;
                    $menu_id = $param2;
                    return SetHakAksesModel::getHakAksesData($pengakses_id, $menu_id);
                }
            },
            'detail hak akses',
            self::ACTION_GET
        );
    }
    public function updateData(Request $request)
    {
        return $this->executeWithAuthAndValidation(
            function($user) use ($request) {
                // Validasi input berdasarkan tipe akses
                $rules = [];
                $messages = [];
                
                // Untuk level hak akses
                if ($request->has('hak_akses_kode')) {
                    $rules = [
                        'hak_akses_kode' => 'required',
                        'menu_akses' => 'required|array'
                    ];
                    $messages = [
                        'hak_akses_kode.required' => 'Kode hak akses wajib diisi',
                        'menu_akses.required' => 'Menu akses wajib diisi',
                        'menu_akses.array' => 'Format menu akses tidak valid'
                    ];
                } 
                // Untuk hak akses individual
                else {
                    // Validasi untuk pattern set_hak_akses_*_*_*
                    foreach ($request->all() as $key => $value) {
                        if (strpos($key, 'set_hak_akses_') === 0) {
                            $parts = explode('_', $key);
                            if (count($parts) >= 5) {
                                $rules[$key] = 'required|boolean';
                            }
                        }
                    }
                    
                    if (empty($rules)) {
                        $rules['pengakses_id'] = 'required';
                        $rules['menu_id'] = 'required';
                    }
                }
    
                // Validate request
                $validator = validator($request->all(), $rules, $messages);
                if ($validator->fails()) {
                    return $this->errorResponse(
                        self::VALIDATION_FAILED, 
                        $validator->errors()->first(), 
                        422
                    );
                }
    
                // Proses update data
                $isLevel = $request->has('hak_akses_kode');
                
                // Add user info to request
                $requestData = $request->all();
                $requestData['updated_by'] = $user->user_id;
                
                // Proses data dengan model
                $result = SetHakAksesModel::updateData($requestData, $isLevel);
                
                if (!$result['success']) {
                    // Gunakan pesan error dari model jika ada, atau gunakan template pesan dari BaseApiController
                    $errorMessage = $result['message'] ?? sprintf(
                        $this->messageTemplates[self::ACTION_UPDATE]['error'], 
                        'hak akses'
                    );
                    return $this->errorResponse($errorMessage, null, 422);
                }
                
                return $result['data'] ?? [];
            },
            'hak akses',
            self::ACTION_UPDATE
        );
    }
}