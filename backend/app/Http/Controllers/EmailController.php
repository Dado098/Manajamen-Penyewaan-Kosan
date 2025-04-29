<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    /**
     * Kirim email untuk tes.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendTestEmail()
    {
        Mail::raw('Tes kirim email dari Laravel!', function ($message) {
            $message->to('mdhapabos@gmail.com')
                    ->subject('Cek Email Laravel');
        });

        return response()->json(['message' => 'Email dikirim!']);
    }
}
