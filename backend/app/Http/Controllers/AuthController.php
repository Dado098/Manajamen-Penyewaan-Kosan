<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Hash;

/**
 * @group Authentication
 *
 * Endpoint untuk Register, Login, dan Logout User.
 */
class AuthController extends Controller
{
        public function verifyEmail(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email sudah diverifikasi.']);
        }

        // Verifikasi email menggunakan hash (ganti getEmailVerificationHash dengan hash_equals + sha1)
        if (! hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json(['message' => 'Verifikasi gagal.'], 400);
        }

        $user->markEmailAsVerified();
        event(new Verified($user));

        return response()->json(['message' => 'Email berhasil diverifikasi.']);
    }

    /**
     * Register user baru.
     *
     * Proses untuk melakukan registrasi user baru dan mengirimkan email verifikasi.
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 201 {
     *  "user": {...},
     *  "token": "1|xxx",
     *  "message": "Registrasi berhasil. Silakan cek email untuk verifikasi."
     * }
     */
    public function register(RegisterRequest $request)
    {
        // Buat user baru dengan email dan verifikasi email
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'no_telp' => $request->no_telp,
            'email' => $request->email, // Menambahkan email
            'password' => bcrypt($request->password),
            'role' => $request->role,
        ]);

        // Kirim email verifikasi
        $user->sendEmailVerificationNotification();

        // Generate token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => new UserResource($user),
            'token' => $token,
            'message' => 'Registrasi berhasil. Silakan cek email untuk verifikasi.',
        ], 201);
    }

    /**
     * Login user.
     *
     * Proses untuk melakukan login user dan memastikan email telah diverifikasi.
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *  "message": "Login berhasil",
     *  "token": "1|xxx"
     * }
     * @response 400 {
     *  "message": "Email belum diverifikasi."
     * }
     */
    public function login(LoginRequest $request)
    {
        $user = User::where('username', $request->username)->first();

        // Jika email belum diverifikasi, kirimkan verifikasi ulang
        if ($user && !$user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email belum diverifikasi.'], 400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
        ]);
    }

    /**
     * Logout user (hapus semua token).
     *
     * Proses untuk logout user dan menghapus semua token akses.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @response 200 {
     *  "message": "Logout berhasil"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }
}
