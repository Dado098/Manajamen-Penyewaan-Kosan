<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranRequest;
use App\Http\Resources\PembayaranResource;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Midtrans\Snap;
use Midtrans\Config;

class PembayaranController extends Controller
{
    public function __construct()
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Menampilkan semua data pembayaran.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pembayarans = Pembayaran::all();
        return response()->json(PembayaranResource::collection($pembayarans), 200);
    }

    /**
     * Menampilkan detail pembayaran.
     *
     * @param int $id
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
     * Menyimpan data pembayaran dan menghasilkan Snap Token dari Midtrans.
     *
     * @param \App\Http\Requests\StorePembayaranRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePembayaranRequest $request)
{
    // Inisialisasi konfigurasi Midtrans
    Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    Config::$isSanitized = true;
    Config::$is3ds = true;

    $data = $request->validated();

    // Tambahkan penyewa_id dari input request
    $data['penyewa_id'] = $request->input('penyewa_id');

    // Tambahkan qr_code UUID
    $data['qr_code'] = Str::uuid();

    // Buat pembayaran
    $pembayaran = Pembayaran::create($data);

    // Ambil ulang model dengan relasi penyewa & pemesanan untuk akses aman
    $pembayaran->load(['penyewa.user', 'pemesanan.kamar']);

    $paymentMethod = $request->input('payment_method'); // "virtual_account" atau "qr_code"
    $allowedBanks = ['cimb', 'bni', 'bri', 'mandiri', 'permata'];
    $selectedBank = $request->input('bank');

    if (!in_array($selectedBank, $allowedBanks)) {
        return response()->json(['message' => 'Metode pembayaran tidak valid untuk bank yang dipilih'], 400);
    }

    $params = [
        'transaction_details' => [
            'order_id' => 'ORDER-' . $pembayaran->id . '-' . now()->timestamp,
            'gross_amount' => (int) $pembayaran->total_tagihan,
        ],
        'customer_details' => [
            'first_name' => $pembayaran->penyewa->nama_penyewa ?? 'Pengguna',
            'email' => $pembayaran->penyewa->email ?? 'dummy@email.com',
            'phone' => $pembayaran->penyewa->phone ?? '08123456789',
        ],
        'item_details' => [
            [
                'id' => 'PEMESANAN-' . $pembayaran->pemesanan->id,
                'price' => (int) $pembayaran->total_tagihan,
                'quantity' => 1,
                'name' => 'Pembayaran Kamar ' . ($pembayaran->pemesanan->kamar->nama ?? 'Kamar Tidak Ditemukan'),
            ]
        ],
        'payment_type' => 'bank_transfer',
        'bank_transfer' => [
            'bank' => $selectedBank,
        ]
    ];

    // Buat Snap Token Midtrans
    $snapToken = Snap::getSnapToken($params);

    // Simpan snap token
    $pembayaran->snap_token = $snapToken;
    $pembayaran->save();

    // Buat URL pembayaran
    $paymentUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/" . $snapToken;

    // QR Code (jika dipilih)
    if ($paymentMethod == 'qr_code') {
        $qrCode = QrCode::size(300)->generate($paymentUrl);
        return response()->json([
            'message' => 'Pembayaran berhasil dibuat',
            'snap_token' => $snapToken,
            'payment_url' => $paymentUrl,
            'qr_code' => base64_encode($qrCode),
            'data' => new PembayaranResource($pembayaran),
        ], 201);
    }

    // Virtual account
    if ($paymentMethod == 'virtual_account') {
        return response()->json([
            'message' => 'Pembayaran berhasil dibuat',
            'snap_token' => $snapToken,
            'payment_url' => $paymentUrl,
            'data' => new PembayaranResource($pembayaran),
        ], 201);
    }

    return response()->json(['message' => 'Metode pembayaran tidak valid'], 400);
}


    /**
     * Generate QR Code dari detail pembayaran.
     *
     * @param int $id
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
