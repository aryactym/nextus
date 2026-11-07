<?php

declare(strict_types=1);

namespace App\Domain\Settings\Http\Controllers;

use App\Domain\Payment\Models\Order;
use App\Domain\Settings\Http\Requests\SendTestEmailRequest;
use App\Domain\Settings\Http\Requests\UpdateSettingsRequest;
use App\Domain\Settings\Mail\TestEmail;
use App\Domain\Settings\Models\Setting;
use App\Http\Controllers\Controller;
use App\Services\UploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Midtrans\Config;
use Midtrans\Snap;

class SettingController extends Controller
{
    /**
     * Setting yang disimpan apa adanya (bukan rahasia).
     *
     * @var array<int, string>
     */
    private const PLAIN_KEYS = [
        'google_analytics_id',
        'google_search_console',
        'facebook_pixel_id',

        'mail_host',
        'mail_port',
        'mail_username',
        'mail_encryption',
        'mail_from_address',
        'mail_from_name',

        'midtrans_client_key',
        'midtrans_is_production',

        'favicon',
    ];

    public function edit(): View
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $settings = [
            'google_analytics_id' => Setting::get('google_analytics_id', ''),
            'google_search_console' => Setting::get('google_search_console', ''),
            'facebook_pixel_id' => Setting::get('facebook_pixel_id', ''),

            'mail_host' => Setting::get('mail_host', ''),
            'mail_port' => Setting::get('mail_port', (string) config('mail.mailers.smtp.port')),
            'mail_username' => Setting::get('mail_username', ''),
            'mail_encryption' => Setting::get('mail_encryption', 'tls'),
            'mail_from_address' => Setting::get('mail_from_address', (string) config('mail.from.address')),
            'mail_from_name' => Setting::get('mail_from_name', (string) config('mail.from.name')),

            'mail_password_set' => Setting::get('mail_password') !== null,

            'midtrans_client_key' => Setting::get('midtrans_client_key', ''),
            'midtrans_is_production' => Setting::get('midtrans_is_production', '0'),
            'midtrans_server_key_set' => Setting::get('midtrans_server_key') !== null,

            'favicon' => Setting::get('favicon', ''),
            'favicon_url' => Setting::get('favicon') ? Storage::disk('public')->url(Setting::get('favicon')) : null,
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(UpdateSettingsRequest $request): RedirectResponse
    {
        $data = $request->validated();

        foreach (self::PLAIN_KEYS as $key) {
            $value = $data[$key] ?? null;
            Setting::put($key, $value === null || $value === '' ? null : (string) $value);
        }

        if ($request->filled('mail_password')) {
            Setting::putSecret('mail_password', (string) $data['mail_password']);
        }

        if ($request->boolean('clear_mail_password')) {
            Setting::put('mail_password', null);
        }

        if ($request->filled('midtrans_server_key')) {
            Setting::putSecret('midtrans_server_key', (string) $data['midtrans_server_key']);
        }

        if ($request->boolean('clear_midtrans_server_key')) {
            Setting::put('midtrans_server_key', null);
        }

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Pengaturan berhasil disimpan.');
    }

    public function sendTestEmail(SendTestEmailRequest $request): RedirectResponse
    {
        $email = (string) $request->validated('email');

        try {
            Mail::to($email)->send(new TestEmail);
        } catch (\Throwable $exception) {
            report($exception);

            return back()->with('error', 'Email uji gagal dikirim: '.$exception->getMessage());
        }

        return back()->with('success', 'Email uji dikirim ke '.$email.'. Periksa kotak masuk atau Mailpit.');
    }

    public function testMidtrans(): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $serverKey = Setting::getSecret('midtrans_server_key');

        if ($serverKey === null || $serverKey === '') {
            return back()->with('error', 'Server key Midtrans belum diisi.');
        }

        $user = auth()->user();
        $amount = 10000.0;

        $order = Order::create([
            'midtrans_order_id' => 'TEST-'.Str::uuid(),
            'user_id' => $user->id,
            'course_id' => null,
            'order_type' => 'integration_test',
            'nature' => 'test',
            'amount' => $amount,
            'currency' => 'IDR',
            'status' => 'pending',
            'expired_at' => now()->addHours(1),
        ]);

        Config::$serverKey = $serverKey;
        Config::$isProduction = Setting::get('midtrans_is_production') === '1';
        Config::$isSanitized = true;
        Config::$is3ds = true;

        try {
            $snapToken = Snap::getSnapToken([
                'transaction_details' => [
                    'order_id' => $order->midtrans_order_id,
                    'gross_amount' => $amount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
                'item_details' => [[
                    'id' => 'test',
                    'price' => $amount,
                    'quantity' => 1,
                    'name' => 'Uji integrasi Midtrans',
                ]],
            ]);
        } catch (\Throwable $exception) {
            $order->delete();
            report($exception);

            return back()->with('error', 'Uji Midtrans gagal: '.$exception->getMessage());
        }

        $order->update(['snap_token' => $snapToken]);

        return redirect()
            ->route('payments.show', $order)
            ->with('success', 'Snap token berhasil dibuat. Klik "Bayar sekarang" untuk membuka modal Midtrans.');
    }

    public function updateFavicon(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->can('manage settings'), 403);

        $request->validate([
            'favicon' => ['required', 'file', 'image', 'max:1024'],
            'clear_favicon' => ['sometimes', 'boolean'],
        ]);

        if ($request->boolean('clear_favicon')) {
            $old = Setting::get('favicon');
            if ($old) {
                Storage::disk('public')->delete($old);
            }
            Setting::put('favicon', null);

            return back()->with('success', 'Favicon berhasil dihapus.');
        }

        $old = Setting::get('favicon');

        try {
            $upload = new UploadService(disk: 'public', folder: 'favicons');
            $result = $upload->upload($request->file('favicon'), 'image');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengunggah favicon: '.$e->getMessage());
        }

        if ($old) {
            Storage::disk('public')->delete($old);
        }

        Setting::put('favicon', $result['path']);

        return back()->with('success', 'Favicon berhasil diperbarui.');
    }
}
