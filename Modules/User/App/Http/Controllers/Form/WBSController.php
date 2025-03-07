<?php

namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WBSController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $kategori = [
            ['id' => 1, 'nama' => 'Diri Sendiri'],
            ['id' => 2, 'nama' => 'Orang Lain'],
        ];

        $pertanyaanForm = [
            [
                "label" => "Informasi yang Dibutuhkan",
                "name" => "pi_informasi_yang_dibutuhkan",
                "type" => "textarea",
                "required" => true
            ],
            [
                "label" => "Alasan Permohonan Informasi",
                "name" => "pi_alasan_permohonan_informasi",
                "type" => "textarea",
                "required" => true
            ],
            [
                "label" => "Sumber Informasi",
                "name" => "pi_sumber_informasi",
                "type" => "checkbox",
                "options" => [
                    "Pertanyaan Langsung Pemohon",
                    "Website / Media Sosial Milik Polinema",
                    "Website / Media Sosial Bukan Milik Polinema"
                ],
                "required" => true
            ],
            [
                "label" => "Alamat Sumber Informasi",
                "name" => "pi_alamat_sumber_informasi",
                "type" => "text",
                "required" => true
            ]
        ];

        return view('user::e-form.wbs', compact('kategori', 'pertanyaanForm'));
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
    public function show($id)
    {
        return view('user::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('user::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, $id): RedirectResponse
    // {

    // }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }
}
