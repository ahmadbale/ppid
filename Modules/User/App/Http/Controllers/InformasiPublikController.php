<?php

namespace Modules\User\App\Http\Controllers;

// use Modules\User\App\Http\Controllers;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Request;

class InformasiPublikController extends Controller
{
    public function index(){
        $pdfFile = 'storage/test-pdfview1.pdf';
        $pdfName = 'Daftar Informasi Publik Polinema.pdf';
        $pdfSize = '1.1 MB';
        $sharedBy = 'superadmin';

        return view('user::informasi-publik.daftar',
        compact('pdfFile', 'pdfName', 'pdfSize', 'sharedBy')
        );
    }
}
