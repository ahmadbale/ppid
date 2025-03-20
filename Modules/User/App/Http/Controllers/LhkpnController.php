<?php

namespace Modules\User\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LHKPNController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Data dasar hukum
        $dasarHukum = [
            'Peraturan Komisi Informasi Republik Indonesia Nomor 1 Tahun 2021 Pasal 15',
            'Keputusan Direktur No. 1228 Tahun 2022 Butir 1'
        ];

        // Data tahun tersedia
        $tahunList = [2022, 2023];

        // Tahun default
        $tahunDipilih = $request->get('tahun', 2022);

        // Data LHKPN berdasarkan tahun
        $lhkpnData = [
            2022 => [
                ['nama' => 'Aang Afandi', 'link' => '#'],
                ['nama' => 'Abdul Rasyid', 'link' => '#'],
                ['nama' => 'Abdullah Helmy', 'link' => '#'],
                ['nama' => 'Agus Suhardono', 'link' => '#'],
                ['nama' => 'Ahmad Hermawan', 'link' => '#'],
            ],
            2023 => [
                ['nama' => 'Bayu Saputra', 'link' => '#'],
                ['nama' => 'Citra Dewi', 'link' => '#'],
                ['nama' => 'Dian Pratama', 'link' => '#'],
                ['nama' => 'Eko Suhendar', 'link' => '#'],
                ['nama' => 'Fajar Santoso', 'link' => '#'],
            ],
        ];

        // Kirim data ke view
        return view('user::LHKPN', [
            'dasarHukum' => $dasarHukum,
            'tahunList' => $tahunList,
            'tahunDipilih' => $tahunDipilih,
            'lhkpnList' => $lhkpnData[$tahunDipilih] ?? []
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('user::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request): RedirectResponse
    // {

    // }

    /**
     * Show the specified resource.
     */
    // public function show($id)
    // {
    //     return view('user::show');
    // }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit($id)
    // {
    //     return view('user::edit');
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id): RedirectResponse
    // {

    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     //
    // }
}
