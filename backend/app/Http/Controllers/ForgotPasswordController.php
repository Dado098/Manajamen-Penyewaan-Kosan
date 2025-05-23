<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\CustomResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || $user->role !== 'penyewa') {
            return response()->json([
                'message' => 'Gagal mengirim link reset. Pastikan email sudah terdaftar dan merupakan penyewa.'
            ], 404);
        }

        // Buat token reset manual dan simpan ke tabel password_resets
        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => bcrypt($token), 'created_at' => Carbon::now()]
        );

        // Generate URL reset password
        $resetUrl = 'http://localhost:5501/frontend-user/html/lupa-password.html?token=' . $token . '&email=' . urlencode($user->email);

        // Kirim email menggunakan Mailable custom
        Mail::to($user->email)->send(new CustomResetPasswordMail($resetUrl));

        return response()->json(['message' => 'Link reset password telah dikirim ke email Anda.']);
    }
}
