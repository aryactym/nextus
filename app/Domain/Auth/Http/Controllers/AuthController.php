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
        $a = random_int(1, 9);
        $b = random_int(1, 9);
        session(['login_captcha_answer' => $a + $b]);

        return view('auth.login', compact('a', 'b'));
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'captcha' => ['required', 'integer'],
        ]);

        if ((int) $request->input('captcha') !== (int) session('login_captcha_answer')) {
            return back()->withErrors([
                'captcha' => 'Jawaban verifikasi salah.',
            ])->onlyInput('email');
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

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
            'role' => ['nullable', 'string', 'max:50'],
            'organization' => ['nullable', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
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
}
