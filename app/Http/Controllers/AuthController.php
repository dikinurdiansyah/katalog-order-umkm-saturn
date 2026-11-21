<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Hitung statistik untuk halaman login (Telemetri)
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();

        // Produksi Hari Ini (Total order masuk hari ini)
        $produksiHariIni = Order::whereBetween('created_at', [$todayStart, $todayEnd])->count();

        // SLA Layanan (Persentase order yang selesai vs total order)
        $totalOrder = Order::count();
        $orderSelesai = Order::where('status', 'SELESAI')->count();
        $slaLayanan = $totalOrder > 0 ? round(($orderSelesai / $totalOrder) * 100, 1) : 100;

        return view('auth.login', compact('produksiHariIni', 'slaLayanan'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (
            $user
            && !$user->isUsingUnsafeSeedPassword($credentials['password'])
            && Hash::check($credentials['password'], $user->password_hash)
        ) {
            Auth::login($user);
            $request->session()->regenerate();

            // PERBAIKAN: Menggunakan route('admin.dashboard') agar tidak error
            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name);
        }

        return back()
            ->withErrors(['email' => 'Email atau password salah.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah logout.');
    }

}
