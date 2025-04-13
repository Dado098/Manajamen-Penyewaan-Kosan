<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Http\Resources\PembayaranResource;
use App\Models\Pembayaran;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PembayaranController extends Controller
{
    /**
     * Menampilkan semua pembayaran.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pembayarans = Pembayaran::all();
        return response()->json(PembayaranResource::collection($pembayarans), 200);
    }

    /**
     * Menampilkan detail pembayaran berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json(new PembayaranResource($pembayaran), 200);
    }

    /**
     * Menyimpan pembayaran baru dan generate QR Code unik.
     *
     * @param  \App\Http\Requests\StorePembayaranRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePembayaranRequest $request)
    {
        $data = $request->validated();
        $data['qr_code'] = Str::uuid();

        $pembayaran = Pembayaran::create($data);

        return response()->json([
            'message' => 'Pembayaran berhasil dibuat',
            'data' => new PembayaranResource($pembayaran),
        ], 201);
    }

    /**
     * Generate QR Code pembayaran dalam format PNG.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function generateQRCode($id)
    {
        $pembayaran = Pembayaran::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $qrCodeUrl = route('pembayarans.show', ['pembayaran' => $pembayaran->id]);
        $qrCode = QrCode::size(300)->format('png')->generate($qrCodeUrl);

        return response($qrCode, 200)->header('Content-Type', 'image/png');
    }
}
