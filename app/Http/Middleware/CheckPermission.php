<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $menu)
    {
        // Jika belum login, tolak
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Cek apakah user memiliki hak akses ke menu tersebut
        if (!Auth::user()->hasPermission($menu)) {
            // Jika tidak punya akses, tendang kembali ke home dengan pesan error
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses ke halaman tersebut!');
        }

        return $next($request);
    }
}