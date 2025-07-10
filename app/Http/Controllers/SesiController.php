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
            'username' => 'required',
            'password' => 'required'
        ],[
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi'   
        ]);

        $infologin = [
            'username' => $request->username,  
            'password' => $request->password
        ];

        if(Auth::attempt($infologin)) {
            $user = Auth::user();
            
            // Redirect based on role
            switch($user->role) {
                case 'admin':
                    return redirect()->route('admin.dashboard');
                case 'reviewer':
                    return redirect()->route('reviewer.dashboard');
                case 'user':
                    return redirect()->route('user.dashboard');
                case 'pengguna':
                    return redirect('/pengguna'); // Legacy route
                default:
                    return redirect('/');
            }
        } else {
            return redirect('sesi')->withErrors('Username atau password salah')->withInput();
        }
    }

    function logout(){
        Auth::logout();
        return redirect('/sesi');
    }
}
