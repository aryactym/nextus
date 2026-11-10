<?php

namespace App\Domain\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * Karakter tanpa huruf/angka ambigu (tanpa 0/O, 1/I/l) agar tetap mudah dibaca pengguna.
     */
    private const CHARSET = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';

    private const CODE_LENGTH = 6;

    private const EXPIRY_MINUTES = 10;

    /**
     * Render gambar CAPTCHA dan simpan kodenya di session.
     */
    public function image(): Response
    {
        $code = $this->generateCode();

        session([
            'captcha_code' => $code,
            'captcha_expires_at' => now()->addMinutes(self::EXPIRY_MINUTES)->getTimestamp(),
        ]);

        return response($this->render($code), 200, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }

    /**
     * @return string kode acak cryptographically secure
     */
    private function generateCode(): string
    {
        $charset = str_split(self::CHARSET);
        $code = '';

        for ($i = 0; $i < self::CODE_LENGTH; $i++) {
            $code .= $charset[random_int(0, count($charset) - 1)];
        }

        return $code;
    }

    /**
     * Gambar kode menjadi PNG dengan noise menggunakan GD.
     *
     * @param  string  $code  kode yang akan dirender
     * @return string biner PNG
     */
    private function render(string $code): string
    {
        $width = 190;
        $height = 60;
        $image = imagecreatetruecolor($width, $height);

        // Background krem (senada --nu-kertas) supaya menyatu dengan form.
        imagefilledrectangle($image, 0, 0, $width, $height, imagecolorallocate($image, 245, 247, 244));

        // Titik noise halus.
        for ($i = 0; $i < 220; $i++) {
            imagesetpixel(
                $image,
                random_int(0, $width - 1),
                random_int(0, $height - 1),
                imagecolorallocate($image, random_int(190, 235), random_int(200, 240), random_int(185, 230)),
            );
        }

        // Garis noise melintasi area teks.
        for ($i = 0; $i < 5; $i++) {
            imageline(
                $image,
                random_int(-10, 30),
                random_int(0, $height),
                $width - random_int(-10, 30),
                random_int(0, $height),
                imagecolorallocate($image, random_int(150, 200), random_int(165, 205), random_int(155, 200)),
            );
        }

        // Karakter dengan posisi vertikal dan warna zamrud gelap acak.
        $x = 14;
        foreach (str_split($code) as $char) {
            imagestring(
                $image,
                5,
                $x,
                random_int(10, 32),
                $char,
                imagecolorallocate($image, random_int(10, 70), random_int(80, 140), random_int(70, 125)),
            );
            $x += 29;
        }

        // Elips noise di atas teks agar lebih sulit di-OCR.
        for ($i = 0; $i < 2; $i++) {
            imagearc(
                $image,
                random_int(20, $width - 20),
                random_int(15, $height - 15),
                random_int(60, 120),
                random_int(15, 35),
                random_int(0, 180),
                random_int(180, 360),
                imagecolorallocate($image, random_int(140, 190), random_int(155, 200), random_int(145, 190)),
            );
        }

        ob_start();
        imagepng($image);
        imagedestroy($image);

        return (string) ob_get_clean();
    }
}
