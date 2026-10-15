<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekRole
{
    // Gunakan ...$roles agar bisa menerima banyak role
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Jika user login dan rolenya ada dalam daftar yang diizinkan
        if (Auth::check() && in_array(Auth::user()->role, $roles)) {
            return $next($request);
        }

        // Jika nekat akses, lempar ke dashboard
        return redirect('dashboard')->with('error', 'Anda tidak punya akses ke halaman tersebut!');
    }
}