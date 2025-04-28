<?php

namespace Modules\User\App\Http\Controllers;

// use Modules\User\App\Http\Controllers;
// use Illuminate\Support\Facades\Http;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Http\Request;

class InformasiDikecualikanController extends Controller
{
    public function index(){
        $pdfFile = 'storage/test-pdfview1.pdf';
        $pdfName = 'Daftar Informasi Dikecualikan.pdf';
        $sharedBy = 'superadmin';

        return view('user::informasi-publik.dikecualikan',
        compact('pdfFile', 'pdfName', 'pdfSize', 'sharedBy')
        );
    }
}
