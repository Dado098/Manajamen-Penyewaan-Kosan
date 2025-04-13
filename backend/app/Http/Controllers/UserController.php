<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Ambil semua user, bisa filter berdasarkan role (penyewa/admin).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Jika ada filter 'role', ambil user berdasarkan role
        if ($request->has('role')) {
            $role = $request->role;

            if (!in_array($role, ['penyewa', 'admin'])) {
                return response()->json(['message' => 'Role tidak valid'], 400);
            }

            $users = User::where('role', $role)->get();
        } else {
            // Jika tidak ada filter, ambil semua user
            $users = User::all();
        }

        return response()->json($users, 200);
    }

    /**
     * Tambah user baru (Register).
     *
     * @param StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreUserRequest $request)
    {
        $validatedData = $request->validated();

        // Enkripsi password sebelum disimpan
        $validatedData['password'] = Hash::make($validatedData['password']);

        $user = User::create($validatedData);

        return response()->json([
            'message' => 'User berhasil dibuat', // Untuk FE: tampilkan notifikasi sukses
            'user' => $user
        ], 201);
    }

    /**
     * Ambil user berdasarkan role tertentu (penyewa/pemilik).
     *
     * @param string $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsersByRole($role)
    {
        if (!in_array($role, ['penyewa', 'pemilik'])) {
            return response()->json(['message' => 'Role tidak valid'], 400);
        }

        $users = User::where('role', $role)->get();

        if ($users->isEmpty()) {
            return response()->json(['message' => 'Tidak ada pengguna dengan role ini'], 404);
        }

        return response()->json($users, 200);
    }

    /**
     * Update data user berdasarkan ID.
     *
     * @param UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $validatedData = $request->validated();

        // Jika password diupdate, enkripsi dulu
        if ($request->filled('password')) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        return response()->json([
            'message' => 'User berhasil diperbarui', // Untuk FE: tampilkan notifikasi sukses
            'user' => $user
        ], 200);
    }

    /**
     * Hapus user berdasarkan ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus'], 200);
    }
}
