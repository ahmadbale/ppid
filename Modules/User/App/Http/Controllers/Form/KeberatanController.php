<?php

namespace Modules\User\App\Http\Controllers\Form;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class KeberatanController extends Controller
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
                'type' => 'select',
                'name' => 'pi_alasan_pengajuan',
                'label' => 'Alasan Pengajuan Keberatan',
                'required' => true,
                'options' => [
                    'Permohonan Informasi Ditolak',
                    'Form Informasi Berkala Tidak Tersedia',
                    'Permintaan Informasi Tidak Dipenuhi',
                    'Permintaan Informasi Ditanggapi Tidak Sebagaimana Yang Diminta',
                    'Biaya Yang Dikenakan Tidak Wajar',
                    'Informasi Yang Disampaikan Melebihi Jangka Waktu Yang Ditentukan',
                ]
            ],
            [
                'type' => 'textarea',
                'name' => 'pi_kasus_posisi',
                'label' => 'Kasus Posisi',
                'required' => true,
                'rows' => 4
            ]
        ];
        return view('user::e-form.keberatan', compact('kategori', 'pertanyaanForm'));
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
