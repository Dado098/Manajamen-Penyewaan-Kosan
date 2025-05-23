<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\User;

class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $resetRecord = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord) {
            return response()->json(['message' => 'Token reset tidak ditemukan.'], 404);
        }

        // Validasi token (dibuat manual, jadi pakai Hash::check)
        if (! Hash::check($request->token, $resetRecord->token)) {
            return response()->json(['message' => 'Token reset tidak valid.'], 400);
        }

        // Token kadaluarsa? (opsional: max 60 menit)
        if (Carbon::parse($resetRecord->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'Token sudah kadaluarsa.'], 400);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus token reset setelah berhasil
        DB::table('password_resets')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil direset.']);
    }
}


