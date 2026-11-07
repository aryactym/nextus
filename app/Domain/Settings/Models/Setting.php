<?php

declare(strict_types=1);

namespace App\Domain\Settings\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * @property string $key
 * @property string|null $value
 */
class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
    ];

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = Cache::rememberForever(self::cacheKey($key), function () use ($key): ?string {
            return static::query()->where('key', $key)->value('value');
        });

        return $value ?? $default;
    }

    public static function put(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::cacheKey($key));
    }

    public static function getSecret(string $key): ?string
    {
        $value = static::get($key);

        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return null;
        }
    }

    public static function putSecret(string $key, ?string $value): void
    {
        static::put($key, $value === null || $value === '' ? null : Crypt::encryptString($value));
    }

    public static function forget(string $key): void
    {
        Cache::forget(self::cacheKey($key));
    }

    private static function cacheKey(string $key): string
    {
        return 'setting.'.$key;
    }
}
