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
use Midtrans\Notification;
use Illuminate\Http\Request;

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
    // Inisialisasi Midtrans
    Config::$serverKey   = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    Config::$isSanitized  = true;
    Config::$is3ds        = true;

    // Validasi dan data awal
    $data = $request->validated();
    $data['penyewa_id'] = $request->input('penyewa_id');
    $data['qr_code']    = Str::uuid();

    // Simpan dulu record minimal
    $pembayaran = Pembayaran::create($data);

    // Generate order_id unik
    $orderId = 'ORDER-' . $pembayaran->id . '-' . now()->timestamp;
    $pembayaran->order_id = $orderId;
    $pembayaran->save();

    // Load relasi
    $pembayaran->load(['penyewa', 'pemesanan.kamar']);

    // Persiapkan param Midtrans
    $params = [
        'transaction_details' => [
            'order_id'     => $orderId,
            'gross_amount' => (int) $pembayaran->total_tagihan,
        ],
        'customer_details'   => [
            'first_name' => $pembayaran->penyewa->name,
            'email'      => $pembayaran->penyewa->email,
            'phone'      => $pembayaran->penyewa->no_telp,
        ],
        'item_details'       => [[
            'id'       => 'PEMESANAN-' . $pembayaran->pemesanan->id,
            'price'    => (int) $pembayaran->total_tagihan,
            'quantity' => 1,
            'name'     => 'Pembayaran Kamar ' . $pembayaran->pemesanan->kamar->nama,
        ]],
        'payment_type'       => 'bank_transfer',
        'bank_transfer'      => [
            'bank' => $request->input('bank'),
        ],
        'callbacks' => [
           'callbacks' => [
             'finish' => ' https://19f9-103-47-133-185.ngrok-free.app/frontend-user/html/thankyou.html',
        ],

                ],
            ];


            // Dapatkan snap token
            $snapToken = Snap::getSnapToken($params);

            // Simpan snap token
            $pembayaran->snap_token = $snapToken;
            $pembayaran->save();

        // Tambahan pada bagian store()
        $response = [
            'message'    => 'Pembayaran berhasil dibuat',
            'order_id'   => $orderId,
            'snap_token' => $snapToken,
            'payment_url'=> "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
            'data'       => new PembayaranResource($pembayaran),
        ];

        return response()->json($response, 201);

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

     /**
     * Webhook handler Midtrans.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notification(Request $request)
{
    $notif = $request->all();

    // Signature key verification
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

    // Temukan pembayaran berdasarkan order_id
    $pembayaran = \App\Models\Pembayaran::where('order_id', $notif['order_id'])->first();

    if (!$pembayaran) {
        return response()->json(['message' => 'Pembayaran tidak ditemukan'], 404);
    }

    // Update status pembayaran
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

    return response()->json(['message' => 'Notification received'], 200);
}


}
