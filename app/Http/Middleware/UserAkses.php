<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserAkses
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $role)
    {
    if (auth()->check() && auth()->user()->role == $role) {
        return $next($request);
    }

    // Redirect berdasarkan role saat ini
    if (auth()->user()->role == 'admin') {
        return redirect('/admin');
    } elseif (auth()->user()->role == 'pengguna') {
        return redirect('/pengguna');
    }

}
}
