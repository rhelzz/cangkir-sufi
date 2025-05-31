<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }
    
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }
        
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }
    
    public function showRegistrationForm()
    {
        if (!Auth::check() || !Auth::user()->isOwner()) {
            // Hanya owner yang bisa akses halaman ini
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }
    
    public function register(Request $request)
    {
        // Hanya owner yang bisa register user baru
        if (!Auth::check() || !Auth::user()->isOwner()) {
            return redirect()->route('dashboard');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // Gunakan aturan bawaan Laravel 9+ untuk password strong
            'password' => [
                'required', 
                'confirmed', 
                Password::min(8)->letters()->numbers()->mixedCase()
            ],
            'role' => ['required', 'in:owner,cashier'],
        ]);
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);
        
        // Tidak auto login user baru, hanya owner yang bisa menambah user
        return redirect()->route('dashboard')->with('success', 'User registered successfully');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
