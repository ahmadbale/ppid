<?php

namespace Modules\Sisfo\App\Http\Controllers;

use Modules\Sisfo\App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    use TraitsController;
    
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Profil',
            'list' => ['Home', 'Profile']
        ];

        $page = (object) [
            'title' => 'Data Profil Pengguna'
        ];

        $activeMenu = 'profile';

        return view('sisfo::profile.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update_pengguna(Request $request, string $id)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'nama_pengguna' => 'required|string|max:100',
            'alamat_pengguna' => 'required|string|max:100',
            'no_hp_pengguna' => 'required|string|unique:m_user,no_hp_pengguna,' . $id . ',user_id',
            'email_pengguna' => 'required|email|unique:m_user,email_pengguna,' . $id . ',user_id',
            'pekerjaan_pengguna' => 'required|string',
            'nik_pengguna' => 'required|string|unique:m_user,nik_pengguna,' . $id . ',user_id',
            'upload_nik_pengguna' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // 10MB max
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:5120', // 5MB max untuk foto profil
        ], [
            'foto_profil.image' => 'File foto profil harus berupa gambar.',
            'foto_profil.mimes' => 'Format foto profil harus jpeg, png, atau jpg.',
            'foto_profil.max' => 'Ukuran foto profil maksimal 5MB.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_type', 'update_profil');
        }

        try {
            // Mengambil pengguna berdasarkan ID
            $user = UserModel::find($id);

            if (!$user) {
                return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
            }

            // Update data pengguna
            $user->nama_pengguna = $request->nama_pengguna;
            $user->email_pengguna = $request->email_pengguna;
            $user->no_hp_pengguna = $request->no_hp_pengguna;
            $user->pekerjaan_pengguna = $request->pekerjaan_pengguna;
            $user->nik_pengguna = $request->nik_pengguna;
            $user->alamat_pengguna = $request->alamat_pengguna;

            // Handle upload foto profil
            if ($request->hasFile('foto_profil')) {
                // Delete old foto profil if exists
                if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                    Storage::delete('public/' . $user->foto_profil);
                }

                // Generate unique filename untuk foto profil
                $file = $request->file('foto_profil');
                $filename = 'foto_profil/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                
                // Store file
                $file->storeAs('public', $filename);
                
                // Save filename to database
                $user->foto_profil = $filename;
            }

            // Handle upload foto KTP
            if ($request->hasFile('upload_nik_pengguna')) {
                // Delete old file if exists
                if ($user->upload_nik_pengguna && Storage::exists('public/' . $user->upload_nik_pengguna)) {
                    Storage::delete('public/' . $user->upload_nik_pengguna);
                }

                // Generate unique filename untuk KTP
                $file = $request->file('upload_nik_pengguna');
                $filename = 'upload_nik/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                
                // Store file
                $file->storeAs('public', $filename);
                
                // Save filename to database
                $user->upload_nik_pengguna = $filename;
            }

            // Simpan perubahan
            $user->save();

            return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Method baru untuk hapus foto profil
    public function delete_foto_profil(string $id)
    {
        try {
            $user = UserModel::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan']);
            }

            // Hapus file foto profil jika ada
            if ($user->foto_profil && Storage::exists('public/' . $user->foto_profil)) {
                Storage::delete('public/' . $user->foto_profil);
            }

            // Reset foto profil di database
            $user->foto_profil = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Foto profil berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    // Method baru untuk hapus foto KTP
    public function delete_foto_ktp(string $id)
    {
        try {
            $user = UserModel::find($id);

            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan']);
            }

            // Hapus file foto KTP jika ada
            if ($user->upload_nik_pengguna && Storage::exists('public/' . $user->upload_nik_pengguna)) {
                Storage::delete('public/' . $user->upload_nik_pengguna);
            }

            // Reset foto KTP di database
            $user->upload_nik_pengguna = null;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Foto KTP berhasil dihapus']);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update_password(Request $request, string $id)
    {
        // Custom validation rules
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:5',
            'new_password_confirmation' => 'required|same:new_password',
        ], [
            'new_password.min' => 'Password minimal harus 5 karakter',
            'new_password_confirmation.same' => 'Verifikasi password yang anda masukkan tidak sesuai dengan password baru',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error_type', 'update_password');
        }

        try {
            // Ambil user berdasarkan ID
            $user = UserModel::find($id);

            if (!$user) {
                return redirect()->back()->with('error', 'Pengguna tidak ditemukan');
            }

            // Cek apakah password lama cocok
            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()
                    ->withErrors(['current_password' => 'Password lama tidak sesuai'])
                    ->with('error_type', 'update_password');
            }

            // Ubah password user
            $user->password = Hash::make($request->new_password);
            $user->save();

            return redirect()->back()->with('success', 'Password berhasil diubah');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->with('error_type', 'update_password');
        }
    }
}