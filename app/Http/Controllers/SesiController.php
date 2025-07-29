<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiController extends Controller
{
    function index(){
        return view('login');
    }

    function login(Request $request){
        $request->validate([
            'nidn' => 'required',
            'password' => 'required'
        ],[
            'nidn.required' => 'NIDN harus diisi',
            'password.required' => 'Password harus diisi'   
        ]);

        $infologin = [
            'nidn' => $request->nidn,  
            'password' => $request->password
        ];

        if(Auth::attempt($infologin)) {
            $user = Auth::user();
            
            // ✅ NEW: Check if using default password (NIDN = password)
            if($request->password === $request->nidn) {
                session()->flash('warning', 'Anda menggunakan password default. Silakan ganti password untuk keamanan.');
            }

            // Redirect based on role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('success', 'Selamat Datang, Super Admin!');
            } else {
            return redirect()->route('user.dashboard')->with('Success', 'Selamat Datang, '. $user->nama. '!');
            }
        }else {
            return redirect()->route('login')
                ->withErrors('NIDN atau password salah')
                ->withInput();
        }
    }

    function logout(Request $request){
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('beranda')->with('success','Logout Berhasil');
    }
}
