<?php
namespace App\Http\Middleware;

use Closure;

class HandleCors
{
    public function handle($request, Closure $next)
    {
        // Handle OPTIONS request (preflight)
        if ($request->getMethod() == "OPTIONS") {
            return response()->json([], 200);
        }

        $response = $next($request);

        // Set CORS headers
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        // Set CSP headers
        $response->headers->set('Content-Security-Policy', "
            default-src 'self';
            script-src 'self' https://app.sandbox.midtrans.com https://cdn.jsdelivr.net 'unsafe-inline' 'unsafe-eval';
            style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net;
            frame-src https://app.sandbox.midtrans.com;
            connect-src https://app.sandbox.midtrans.com ws://127.0.0.1:5500;
        ");

        return $response;
    }
}
