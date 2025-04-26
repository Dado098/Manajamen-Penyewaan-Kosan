<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranPerpanjanganRequest;
use App\Http\Resources\PembayaranPerpanjanganResource;
use App\Models\PembayaranPerpanjangan;
use App\Models\Pemesanan;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Midtrans\Snap;
use Midtrans\Config;
use Midtrans\Notification;

class PembayaranPerpanjanganController extends Controller
{
    /**
     * Konfigurasi Midtrans ketika controller diinisialisasi.
     */
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Menampilkan daftar semua pembayaran perpanjangan.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $pembayaran = PembayaranPerpanjangan::all();
        return response()->json(PembayaranPerpanjanganResource::collection($pembayaran), 200);
    }

    /**
     * Menampilkan detail pembayaran perpanjangan berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $pembayaran = PembayaranPerpanjangan::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        return response()->json(new PembayaranPerpanjanganResource($pembayaran), 200);
    }

    /**
     * Membuat pembayaran perpanjangan baru dan menghasilkan Snap Token Midtrans.
     *
     * @param  \App\Http\Requests\StorePembayaranPerpanjanganRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePembayaranPerpanjanganRequest $request)
    {
        $data = $request->validated();
        $data['qr_code'] = Str::uuid();
        $data['status']  = 'proses';

        $pembayaran = PembayaranPerpanjangan::create($data);

        $pembayaran->load('pemesanan.kamar'); // Load relasi

        // Generate order_id unik
        $orderId = 'ORDER-EXTEND-' . $pembayaran->id . '-' . now()->timestamp;
        $pembayaran->order_id = $orderId;
        $pembayaran->save();

        // Siapkan parameter Snap
        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) $pembayaran->total_tagihan,
            ],
            'customer_details'   => [
                'first_name' => $pembayaran->pemesanan->penyewa->name ?? 'Penyewa',
                'email'      => $pembayaran->pemesanan->penyewa->email ?? 'no-email@example.com',
                'phone'      => $pembayaran->pemesanan->penyewa->no_telp ?? '08123456789',
            ],
            'item_details'       => [[
                'id'       => 'PERPANJANGAN-' . $pembayaran->pemesanan->id,
                'price'    => (int) $pembayaran->total_tagihan,
                'quantity' => 1,
                'name'     => 'Perpanjangan Kamar ' . ($pembayaran->pemesanan->kamar->nama ?? ''),
            ]],
        ];

        $snapToken = Snap::getSnapToken($params);

        $pembayaran->snap_token = $snapToken;
        $pembayaran->save();

        return response()->json([
            'message'    => 'Pembayaran perpanjangan berhasil dibuat',
            'order_id'   => $orderId,
            'snap_token' => $snapToken,
            'payment_url'=> "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
            'data'       => new PembayaranPerpanjanganResource($pembayaran),
        ], 201);
    }

    /**
     * Generate QR Code untuk pembayaran perpanjangan berdasarkan ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function generateQRCode($id)
    {
        $pembayaran = PembayaranPerpanjangan::find($id);

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        $qrCodeUrl = route('pembayaran-perpanjangan.show', ['id' => $pembayaran->id]);
        $qrCode = QrCode::size(300)->format('png')->generate($qrCodeUrl);

        return response($qrCode, 200)->header('Content-Type', 'image/png');
    }

    /**
     * Menangani notifikasi pembayaran dari Midtrans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notification(Request $request)
    {
        $notif = $request->all();

        $serverKey = config('midtrans.server_key');
        $expectedSignature = hash('sha512',
            $notif['order_id'] .
            $notif['status_code'] .
            $notif['gross_amount'] .
            $serverKey
        );

        if ($notif['signature_key'] !== $expectedSignature) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pembayaran = PembayaranPerpanjangan::where('order_id', $notif['order_id'])->first();

        if (!$pembayaran) {
            return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
        }

        switch ($notif['transaction_status']) {
            case 'settlement':
                $pembayaran->status = 'sukses';
                break;
            case 'pending':
                $pembayaran->status = 'proses';
                break;
            case 'deny':
            case 'cancel':
            case 'expire':
                $pembayaran->status = 'gagal';
                break;
            default:
                $pembayaran->status = 'proses';
        }

        $pembayaran->save();

        return response()->json(['message' => 'Notification processed successfully'], 200);
    }
}
