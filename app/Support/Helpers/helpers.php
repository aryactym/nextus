<?php

if (!function_exists('organization_id')) {
    /**
     * Ambil organization_id aktif dari session, default 1 (organisasi tunggal MVP).
     */
    function organization_id(): ?int
    {
        return \Illuminate\Support\Facades\Session::get('organization_id', 1);
    }
}

if (!function_exists('format_date_id')) {
    /**
     * Format tanggal bahasa Indonesia: 06 Nov 2026.
     */
    function format_date_id($date): string
    {
        return \Illuminate\Support\Carbon::parse($date)->locale('id')->isoFormat('D MMMM YYYY');
    }
}

if (!function_exists('format_date_en')) {
    /**
     * Format tanggal bahasa Inggris: Nov 06, 2026.
     */
    function format_date_en($date): string
    {
        return \Illuminate\Support\Carbon::parse($date)->locale('en')->isoFormat('MMMM D, YYYY');
    }
}

if (!function_exists('currency_idr')) {
    function currency_idr(float $amount): string
    {
        return number_format($amount, 0, ',', '.');
    }
}
