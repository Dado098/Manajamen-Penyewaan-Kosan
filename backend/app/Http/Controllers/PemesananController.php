<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePemesananRequest;
use App\Http\Resources\PemesananResource;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Pemesanan
 *
 * API untuk manajemen data Pemesanan
 */
class PemesananController extends Controller
{
    /**
     * Menampilkan daftar semua pemesanan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pemesanans = Pemesanan::with('kamar:id,image')->get();

        return response()->json(PemesananResource::collection($pemesanans), 200);
    }

    /**
     * Menampilkan detail pemesanan berdasarkan ID.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pemesanan = Pemesanan::with('kamar:id,image')->find($id);

        if (!$pemesanan) {
            return response()->json(['message' => 'Pemesanan tidak ditemukan'], 404);
        }

        return response()->json(new PemesananResource($pemesanan), 200);
    }

    /**
     * Membuat pemesanan baru.
     *
     * @param \App\Http\Requests\StorePemesananRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePemesananRequest $request)
    {
        $user = Auth::user(); // atau auth()->user()

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validated();
        $validated['penyewa_id'] = $user->id;

        $pemesanan = Pemesanan::create($validated);

        return response()->json(new PemesananResource($pemesanan), 201);
    }
}
