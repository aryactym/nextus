<?php

declare(strict_types=1);

namespace App\Domain\Settings\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage settings') ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'google_analytics_id' => ['nullable', 'string', 'max:32', 'regex:/^(G-[A-Z0-9]{4,20}|UA-\d{4,12}-\d{1,4}|GTM-[A-Z0-9]{4,20})$/i'],
            'google_search_console' => ['nullable', 'string', 'max:255'],
            'facebook_pixel_id' => ['nullable', 'string', 'max:32', 'regex:/^\d{5,20}$/'],

            'mail_host' => ['nullable', 'string', 'max:255'],
            'mail_port' => ['nullable', 'integer', 'between:1,65535'],
            'mail_username' => ['nullable', 'string', 'max:255'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'clear_mail_password' => ['sometimes', 'boolean'],
            'mail_encryption' => ['nullable', 'string', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:255'],
            'mail_from_name' => ['nullable', 'string', 'max:255'],

            'midtrans_server_key' => ['nullable', 'string', 'max:255'],
            'midtrans_client_key' => ['nullable', 'string', 'max:255'],
            'midtrans_is_production' => ['sometimes', 'boolean'],
            'clear_midtrans_server_key' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'google_analytics_id' => 'ID Google Analytics',
            'google_search_console' => 'kode Search Console',
            'facebook_pixel_id' => 'ID Facebook Pixel',
            'mail_host' => 'host SMTP',
            'mail_port' => 'port SMTP',
            'mail_username' => 'username SMTP',
            'mail_password' => 'password/API key SMTP',
            'mail_encryption' => 'enkripsi SMTP',
            'mail_from_address' => 'alamat pengirim',
            'mail_from_name' => 'nama pengirim',
        ];
    }
}
