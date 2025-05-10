<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Illuminate\Validation\ValidationException;

trait BaseControllerFunction
{
    protected function redirectSuccess($route, $message = 'Data berhasil diproses', array $additionalParams = [])
    {
        $params = ['success' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect($route)->with($sessionParams);
    }
    
    protected function redirectError($message, array $additionalParams = [])
    {
        $params = ['error' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->with($sessionParams)->withInput();
    }
    
    protected function redirectValidationError(ValidationException $e, array $additionalParams = [])
    {
        $params = [];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->withErrors($e->validator)->with($sessionParams)->withInput();
    }
    
    protected function redirectException(\Exception $e, $prefix = 'Terjadi kesalahan', array $additionalParams = [])
    {
        $message = $prefix . ': ' . $e->getMessage();
        $params = ['error' => $message];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $sessionParams = array_merge($params, $additionalParams);
        
        return redirect()->back()->with($sessionParams)->withInput();
    }
    
    protected function jsonSuccess($data, $message = 'Data berhasil diproses', $statusCode = 200, array $additionalParams = [])
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
    
    protected function jsonValidationError(ValidationException $e, $statusCode = 422, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => 'Validasi gagal',
            'errors' => $e->errors()
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
    
    protected function jsonError(\Exception $e, $prefix = 'Terjadi kesalahan', $statusCode = 500, array $additionalParams = [])
    {
        $response = [
            'success' => false,
            'message' => $prefix . ': ' . $e->getMessage()
        ];
        
        // Menggabungkan parameter tambahan ke dalam respons
        $responseData = array_merge($response, $additionalParams);
        
        return response()->json($responseData, $statusCode);
    }
}