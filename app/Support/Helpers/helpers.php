<?php

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Session;

if (! function_exists('organization_id')) {
    /**
     * Ambil organization_id aktif.
     * Prioritas: 1) user yang sedang login, 2) session, 3) null (tidak scope).
     */
    function organization_id(): ?int
    {
        // Jangan panggil auth()->check()/user() di sini: scope global User memanggil
        // helper ini, sehingga resolusi user (retrieveById) akan memicunya lagi
        // dan berujung rekursi tak berujung / memory exhausted.
        // hasUser() hanya mengecek apakah user sudah di-resolve, tanpa query baru.
        if (auth()->hasUser()) {
            return (int) auth()->user()->organization_id;
        }

        // Gunakan org dari session hanya jika di-set secara eksplisit
        $fromSession = Session::get('organization_id');

        return $fromSession !== null ? (int) $fromSession : null;
    }
}

if (! function_exists('format_date_id')) {
    /**
     * Format tanggal bahasa Indonesia: 06 Nov 2026.
     */
    function format_date_id($date): string
    {
        return Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY');
    }
}

if (! function_exists('format_date_en')) {
    /**
     * Format tanggal bahasa Inggris: Nov 06, 2026.
     */
    function format_date_en($date): string
    {
        return Carbon::parse($date)->locale('en')->isoFormat('MMMM D, YYYY');
    }
}

if (! function_exists('currency_idr')) {
    function currency_idr(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}

if (! function_exists('current_locale')) {
    /**
     * Ambil locale aktif dari aplikasi.
     */
    function current_locale(): string
    {
        return app()->getLocale();
    }
}
