<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{

    public function index(Request $request)
    {
        return view('profile', [
            'user' => $request->user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        // Validasi input menggunakan validator
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'required|email'
        ]);

        // Jika validasi gagal, kembalikan pesan kesalahan
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        try {
            $user = Auth::user();
            $user->phone = $request->input('phone');
            $user->address = $request->input('address');
            $user->email = $request->email;
            

            $storagePath = 'employee'; // Ganti dengan alamat penyimpanan yang sesuai
            $uploadedFileName = $user->handleFileUpload($request, 'picture', $storagePath);
            
          

            if ($uploadedFileName !== null) {
                $user->picture = $uploadedFileName;
            }
            $user->save();

            return response()->json(['message' => 'Profil Anda telah diperbarui'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan saat memperbarui profil: ' . $e->getMessage()], 500);
        }
        
    }
    
    public function updatePassword(Request $request)
    {
        // Validasi input menggunakan Validator
        $validator = Validator::make($request->all(), [
            'currentPassword' => 'required|string|min:6',
            'newPassword' => 'required|string|min:6',
            'confirmPassword' => 'required_with:newPassword|same:newPassword|min:6'
        ]);

        // Cek apakah validasi gagal
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Dapatkan pengguna yang sedang masuk
        $user = Auth::user();

        // Periksa apakah kata sandi saat ini benar
        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json(['error' => 'Kata sandi saat ini tidak cocok.'], 400);
        }

        // Perbarui kata sandi pengguna
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json(['message' => 'Kata sandi Anda telah diperbarui.'], 200);
    }
}
