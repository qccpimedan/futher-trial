<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
   public function showLoginForm()
{
    // Jika user sudah login, redirect ke dashboard
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    
    return view('auth.login');
}

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Username atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        // Check if the login field is email or username
        $loginField = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        // Cek user berdasarkan email atau username
        $user = \App\Models\User::where($loginField, $request->input('login'))->first();

        if ($user && Hash::check($request->input('password'), $user->password)) {
            // Cek apakah email sudah diverifikasi
            if (is_null($user->email_verified_at)) {
                return back()->withInput($request->only('login'))->with('error', 'Email Anda belum diaktivasi. Silakan hubungi administrator untuk mengaktivasi akun Anda.');
            }
            
            Auth::login($user, $request->filled('remember'));
            $request->session()->regenerate();
            
            return redirect()->intended('/dashboard');
        }

        return back()->withInput($request->only('login'))->with('error', 'Username/Email atau password salah.');
    }
    
}