<?php

namespace App\Domain\Auth\Http\Controllers;

use App\Domain\Auth\Mail\WelcomeEmail;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'string', 'max:12'],
        ]);

        if (! $this->verifyCaptcha((string) $request->input('captcha'))) {
            return back()->withErrors([
                'captcha' => 'Kode verifikasi salah atau kedaluwarsa. Muat ulang gambar lalu coba lagi.',
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Set session organization_id sesuai user yang login
            session(['organization_id' => auth()->user()->organization_id]);

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang.');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi salah.',
        ])->onlyInput('email');
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'pendidikan_id' => ['nullable', 'exists:pendidikans,id'],
            'nama_jabatan' => ['nullable', 'string', 'max:255'],
            'kategori_jabatan' => ['nullable', 'string', 'max:100'],
            'nama_organisasi' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
            'captcha' => ['required', 'string', 'max:12'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (! $this->verifyCaptcha((string) $request->input('captcha'))) {
            return back()->withErrors([
                'captcha' => 'Kode verifikasi salah atau kedaluwarsa. Muat ulang gambar lalu coba lagi.',
            ])->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pendidikan_id' => $request->pendidikan_id,
            'nama_jabatan' => $request->nama_jabatan,
            'kategori_jabatan' => $request->kategori_jabatan,
            'nama_organisasi' => $request->nama_organisasi,
            'locale' => 'id',
        ]);
        $user->assignRole(Role::where('name', 'Learner')->first() ?? 'Learner');

        Mail::to($user)->send(new WelcomeEmail($user));

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Akun berhasil dibuat.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Verifikasi kode CAPTCHA GD dari session: case-insensitive, berlaku sekali pakai,
     * dan kedaluwarsa sesuai batas waktu di CaptchaController.
     *
     * @param  string  $input  jawaban yang dikirim pengguna
     */
    private function verifyCaptcha(string $input): bool
    {
        $code = session('captcha_code');
        $expiresAt = session('captcha_expires_at');

        // Kode hanya berlaku untuk satu kali percobaan agar tidak bisa di-brute force.
        session()->forget(['captcha_code', 'captcha_expires_at']);

        return $code !== null
            && $expiresAt !== null
            && $expiresAt >= now()->getTimestamp()
            && hash_equals(strtoupper($code), strtoupper(trim($input)));
    }
}
