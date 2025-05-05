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
    public function index(Request $request)
{
    $user = $request->user(); // Dapatkan user dari token

    if ($user->role === 'pemilik') {
        // Admin bisa melihat semua pembayaran dan nama penyewa
        $pembayarans = Pembayaran::with('penyewa')
            ->orderBy('updated_at', 'desc')
            ->get();
    } else {
        // Penyewa hanya melihat pembayarannya sendiri
        $pembayarans = Pembayaran::with('penyewa') // tetap bisa include nama sendiri
            ->where('penyewa_id', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    return response()->json($pembayarans);
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
    // 1. Konfigurasi Midtrans
    Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
    Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
    Config::$isSanitized  = true;
    Config::$is3ds        = true;

    // 2. Validasi & data awal
    $data = $request->validated();
    $data['penyewa_id'] = $request->input('penyewa_id');
    $data['qr_code']    = Str::uuid();

    // 3. Simpan pembayaran awal
    $pembayaran = Pembayaran::create([
        'pemesanan_id'      => $data['pemesanan_id'] ?? null,
        'penyewa_id'        => $data['penyewa_id'],
        'total_tagihan'     => $data['total_tagihan'],
        'status'            => 'menunggu pembayaran',
        'qr_code'           => $data['qr_code'],
        'metode_pembayaran' => 'Bank Transfer',
    ]);

    // 4. Generate order ID unik
    $orderId = 'ORDER-' . $pembayaran->id . '-' . now()->timestamp;
    $pembayaran->order_id = $orderId;
    $pembayaran->save();

    // 5. Load relasi yang ada
    $pembayaran->load('penyewa');
    if ($pembayaran->pemesanan_id) {
        $pembayaran->load('pemesanan.kamar');
    }

    // 6. Setup parameter Snap
    $params = [
        'transaction_details' => [
            'order_id'     => $orderId,
            'gross_amount' => (int) $pembayaran->total_tagihan,
        ],
        'customer_details' => [
            'first_name' => $pembayaran->penyewa->name,
            'email'      => $pembayaran->penyewa->email,
            'phone'      => $pembayaran->penyewa->no_telp,
        ],
        'callbacks' => [
            'finish' => 'http://127.0.0.1:5500/frontend-user/html/konfirmasi.html',
        ],
    ];

    // Tambahkan item_details hanya jika ada pemesanan
    if ($pembayaran->pemesanan_id && $pembayaran->pemesanan) {
        $params['item_details'][] = [
            'id'       => 'PEMESANAN-' . $pembayaran->pemesanan->id,
            'price'    => (int) $pembayaran->total_tagihan,
            'quantity' => 1,
            'name'     => 'Pembayaran Kamar ' . $pembayaran->pemesanan->kamar->nama,
        ];
    } else {
        $params['item_details'][] = [
            'id'       => 'PEMBAYARAN-' . $pembayaran->id,
            'price'    => (int) $pembayaran->total_tagihan,
            'quantity' => 1,
            'name'     => 'Pembayaran Umum',
        ];
    }

    // 7. Snap Token
    $snapToken = Snap::getSnapToken($params);

    // 8. Simpan Snap Token
    $pembayaran->snap_token = $snapToken;
    $pembayaran->save();

    // 9. Response
    return response()->json([
        'message'     => 'Pembayaran berhasil dibuat',
        'order_id'    => $orderId,
        'snap_token'  => $snapToken,
        'payment_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$snapToken}",
        'data'        => new PembayaranResource($pembayaran),
    ], 201);
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
