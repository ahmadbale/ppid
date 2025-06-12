<?php 
namespace Modules\Sisfo\App\Http\Controllers\Api\ManagePengguna;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Modules\Sisfo\App\Models\UserModel;
use Modules\Sisfo\App\Http\Controllers\Api\BaseApiController;

class ApiProfileController extends BaseApiController
{
    public function index(Request $request)
    {
        return $this->executeWithAuthentication(
            function ($user) {
                // Ambil data profil user yang sedang login
                $profileData = UserModel::with(['level', 'hakAkses'])->find($user->user_id);
                
                if (!$profileData) {
                    return null;
                }
                
                return $profileData;
            },
            'profil pengguna',
            self::ACTION_GET
        );
    }

    public function updatePengguna(Request $request, $id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request, $id) {
                // Validasi bahwa user hanya bisa update profil sendiri (kecuali admin)
                if ($user->user_id != $id && (!$user->level || $user->level->hak_akses_kode !== 'SAR')) {
                    return $this->errorResponse(
                        'AUTH_FORBIDDEN',
                        'Anda tidak memiliki izin untuk mengubah profil pengguna lain',
                        self::HTTP_FORBIDDEN
                    );
                }

                // Ambil user berdasarkan ID terlebih dahulu
                $targetUser = UserModel::find($id);
                if (!$targetUser) {
                    return $this->errorResponse(
                        'Pengguna tidak ditemukan',
                        null,
                        self::HTTP_NOT_FOUND
                    );
                }

                // VALIDASI DAN UPDATE LANGSUNG TANPA KOMPLEKSITAS
                $updateData = [];
                
                // Validasi dan set data yang akan diupdate
                if ($request->filled('nama_pengguna')) {
                    $updateData['nama_pengguna'] = $request->nama_pengguna;
                }
                
                if ($request->filled('alamat_pengguna')) {
                    $updateData['alamat_pengguna'] = $request->alamat_pengguna;
                }
                
                if ($request->filled('no_hp_pengguna')) {
                    // Check uniqueness
                    $exists = UserModel::where('no_hp_pengguna', $request->no_hp_pengguna)
                        ->where('user_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['no_hp_pengguna' => ['Nomor HP sudah digunakan']]
                        );
                    }
                    $updateData['no_hp_pengguna'] = $request->no_hp_pengguna;
                }
                
                if ($request->filled('email_pengguna')) {
                    // Check uniqueness
                    $exists = UserModel::where('email_pengguna', $request->email_pengguna)
                        ->where('user_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['email_pengguna' => ['Email sudah digunakan']]
                        );
                    }
                    $updateData['email_pengguna'] = $request->email_pengguna;
                }
                
                if ($request->filled('pekerjaan_pengguna')) {
                    $updateData['pekerjaan_pengguna'] = $request->pekerjaan_pengguna;
                }
                
                if ($request->filled('nik_pengguna')) {
                    // Check uniqueness
                    $exists = UserModel::where('nik_pengguna', $request->nik_pengguna)
                        ->where('user_id', '!=', $id)
                        ->exists();
                    if ($exists) {
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['nik_pengguna' => ['NIK sudah digunakan']]
                        );
                    }
                    $updateData['nik_pengguna'] = $request->nik_pengguna;
                }

                // Handle upload foto profil
                if ($request->hasFile('foto_profil')) {
                    $file = $request->file('foto_profil');
                    
                    // Validasi file
                    if (!in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['foto_profil' => ['Format file harus JPG, JPEG, atau PNG']]
                        );
                    }
                    
                    if ($file->getSize() > 5242880) { // 5MB
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['foto_profil' => ['Ukuran file maksimal 5MB']]
                        );
                    }

                    // Delete old foto profil if exists
                    if ($targetUser->foto_profil && Storage::exists('public/' . $targetUser->foto_profil)) {
                        Storage::delete('public/' . $targetUser->foto_profil);
                    }

                    // Generate unique filename
                    $filename = 'foto_profil/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                    
                    // Store file
                    $file->storeAs('public', $filename);
                    
                    $updateData['foto_profil'] = $filename;
                }

                // Handle upload foto KTP
                if ($request->hasFile('upload_nik_pengguna')) {
                    $file = $request->file('upload_nik_pengguna');
                    
                    // Validasi file
                    if (!in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['upload_nik_pengguna' => ['Format file harus JPG, JPEG, atau PNG']]
                        );
                    }
                    
                    if ($file->getSize() > 10485760) { // 10MB
                        return $this->errorResponse(
                            'Validasi gagal',
                            null,
                            self::HTTP_UNPROCESSABLE_ENTITY,
                            ['upload_nik_pengguna' => ['Ukuran file maksimal 10MB']]
                        );
                    }

                    // Delete old file if exists
                    if ($targetUser->upload_nik_pengguna && Storage::exists('public/' . $targetUser->upload_nik_pengguna)) {
                        Storage::delete('public/' . $targetUser->upload_nik_pengguna);
                    }

                    // Generate unique filename
                    $filename = 'upload_nik/' . Str::random(40) . '.' . $file->getClientOriginalExtension();
                    
                    // Store file
                    $file->storeAs('public', $filename);
                    
                    $updateData['upload_nik_pengguna'] = $filename;
                }

                // UPDATE DATABASE MENGGUNAKAN QUERY BUILDER
                if (!empty($updateData)) {
                    $updated = UserModel::where('user_id', $id)->update($updateData);
                    
                    if (!$updated) {
                        return $this->errorResponse(
                            'Gagal menyimpan perubahan ke database',
                            null,
                            self::HTTP_INTERNAL_SERVER_ERROR
                        );
                    }
                }

                // Return fresh data dengan relasi
                $updatedUser = UserModel::with(['level', 'hakAkses'])->find($id);
                return $updatedUser;
            },
            'profil pengguna',
            self::ACTION_UPDATE
        );
    }

    public function updatePassword(Request $request, $id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($request, $id) {
                // Validasi bahwa user hanya bisa update password sendiri (kecuali admin)
                if ($user->user_id != $id && (!$user->level || $user->level->hak_akses_kode !== 'SAR')) {
                    return $this->errorResponse(
                        'AUTH_FORBIDDEN',
                        'Anda tidak memiliki izin untuk mengubah password pengguna lain',
                        self::HTTP_FORBIDDEN
                    );
                }

                // Validasi input 
                if (empty($request->current_password)) {
                    return $this->errorResponse(
                        'Validasi gagal',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        ['current_password' => ['Password lama wajib diisi']]
                    );
                }

                if (empty($request->new_password) || strlen($request->new_password) < 5) {
                    return $this->errorResponse(
                        'Validasi gagal',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        ['new_password' => ['Password baru minimal 5 karakter']]
                    );
                }

                if ($request->new_password !== $request->new_password_confirmation) {
                    return $this->errorResponse(
                        'Validasi gagal',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        ['new_password_confirmation' => ['Konfirmasi password tidak cocok']]
                    );
                }

                // Ambil user berdasarkan ID
                $targetUser = UserModel::find($id);
                if (!$targetUser) {
                    return $this->errorResponse(
                        'Pengguna tidak ditemukan',
                        null,
                        self::HTTP_NOT_FOUND
                    );
                }

                // Cek apakah password lama cocok
                if (!Hash::check($request->current_password, $targetUser->password)) {
                    return $this->errorResponse(
                        'Password lama tidak sesuai',
                        null,
                        self::HTTP_UNPROCESSABLE_ENTITY,
                        ['current_password' => ['Password lama tidak sesuai']]
                    );
                }

                // Update password menggunakan query builder
                $updated = UserModel::where('user_id', $id)->update([
                    'password' => Hash::make($request->new_password)
                ]);

                if (!$updated) {
                    return $this->errorResponse(
                        'Gagal mengubah password',
                        null,
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return ['message' => 'Password berhasil diubah'];
            },
            'password pengguna',
            self::ACTION_UPDATE
        );
    }

    public function deleteFotoProfil($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                // Validasi bahwa user hanya bisa hapus foto profil sendiri (kecuali admin)
                if ($user->user_id != $id && (!$user->level || $user->level->hak_akses_kode !== 'SAR')) {
                    return $this->errorResponse(
                        'AUTH_FORBIDDEN',
                        'Anda tidak memiliki izin untuk menghapus foto profil pengguna lain',
                        self::HTTP_FORBIDDEN
                    );
                }

                $targetUser = UserModel::find($id);
                if (!$targetUser) {
                    return $this->errorResponse(
                        'Pengguna tidak ditemukan',
                        null,
                        self::HTTP_NOT_FOUND
                    );
                }

                // Hapus file foto profil jika ada
                if ($targetUser->foto_profil && Storage::exists('public/' . $targetUser->foto_profil)) {
                    Storage::delete('public/' . $targetUser->foto_profil);
                }

                // Reset foto profil di database menggunakan query builder
                $updated = UserModel::where('user_id', $id)->update(['foto_profil' => null]);

                if (!$updated) {
                    return $this->errorResponse(
                        'Gagal menghapus foto profil',
                        null,
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return ['message' => 'Foto profil berhasil dihapus'];
            },
            'foto profil',
            self::ACTION_DELETE
        );
    }

    public function deleteFotoKtp($id)
    {
        return $this->executeWithAuthentication(
            function ($user) use ($id) {
                // Validasi bahwa user hanya bisa hapus foto KTP sendiri (kecuali admin)
                if ($user->user_id != $id && (!$user->level || $user->level->hak_akses_kode !== 'SAR')) {
                    return $this->errorResponse(
                        'AUTH_FORBIDDEN',
                        'Anda tidak memiliki izin untuk menghapus foto KTP pengguna lain',
                        self::HTTP_FORBIDDEN
                    );
                }

                $targetUser = UserModel::find($id);
                if (!$targetUser) {
                    return $this->errorResponse(
                        'Pengguna tidak ditemukan',
                        null,
                        self::HTTP_NOT_FOUND
                    );
                }

                // Hapus file foto KTP jika ada
                if ($targetUser->upload_nik_pengguna && Storage::exists('public/' . $targetUser->upload_nik_pengguna)) {
                    Storage::delete('public/' . $targetUser->upload_nik_pengguna);
                }

                // Reset foto KTP di database menggunakan query builder
                $updated = UserModel::where('user_id', $id)->update(['upload_nik_pengguna' => null]);

                if (!$updated) {
                    return $this->errorResponse(
                        'Gagal menghapus foto KTP',
                        null,
                        self::HTTP_INTERNAL_SERVER_ERROR
                    );
                }

                return ['message' => 'Foto KTP berhasil dihapus'];
            },
            'foto KTP',
            self::ACTION_DELETE
        );
    }
}