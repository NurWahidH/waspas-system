<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'Email atau Username harus diisi',
            'password.required' => 'Password harus diisi',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        $credentials = [
            $loginField => $request->login,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            
            // Check if email is verified
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
            }
            
            return redirect()->intended(route('dashboard'))
                ->with('success', 'Selamat datang, ' . Auth::user()->nama_lengkap . '!');
        }

        return redirect()->back()
            ->withErrors(['login' => 'Email/Username atau password salah'])
            ->withInput();
    }

    // Tampilkan halaman register
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users|alpha_dash',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah terdaftar',
            'username.alpha_dash' => 'Username hanya boleh mengandung huruf, angka, dan underscore',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Trigger email verification
        event(new Registered($user));
        
        Auth::login($user);

        return redirect()->route('verification.notice')
            ->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi akun.');
    }

    // Show email verification notice
    public function showVerificationNotice()
    {
        if (Auth::user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.verify-email');
    }

    // Handle email verification
    public function verifyEmail(EmailVerificationRequest $request)
    {
        $request->fulfill();

        return redirect()->route('dashboard')
            ->with('success', 'Email berhasil diverifikasi! Selamat datang, ' . Auth::user()->nama_lengkap . '!');
    }

    // Resend verification email
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda!');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil logout');
    }
}