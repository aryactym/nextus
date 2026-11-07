<?php

return [

    /*
    |--------------------------------------------------------------------------
    | XP per peristiwa
    |--------------------------------------------------------------------------
    */

    'xp' => [
        'lesson' => 10,
        'quiz' => 25,
        'final_assessment' => 100,
        'course' => 150,
    ],

    /*
    |--------------------------------------------------------------------------
    | Ambang level (XP kumulatif)
    |--------------------------------------------------------------------------
    */

    'levels' => [
        ['level' => 1, 'name_id' => 'Pemula', 'name_en' => 'Beginner', 'threshold' => 0],
        ['level' => 2, 'name_id' => 'Penyimak', 'name_en' => 'Listener', 'threshold' => 100],
        ['level' => 3, 'name_id' => 'Pelaku', 'name_en' => 'Doer', 'threshold' => 300],
        ['level' => 4, 'name_id' => 'Penggerak', 'name_en' => 'Mover', 'threshold' => 600],
        ['level' => 5, 'name_id' => 'Pegiat', 'name_en' => 'Activist', 'threshold' => 1000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tier badge Tuntas (jumlah pelajaran selesai)
    |--------------------------------------------------------------------------
    */

    'tuntas_tiers' => [
        'default' => 5,
        'bronze' => 25,
        'silver' => 100,
        'gold' => 400,
    ],

    /*
    |--------------------------------------------------------------------------
    | Daftar badge
    |--------------------------------------------------------------------------
    */

    'badges' => [
        [
            'slug' => 'tuntas',
            'name_id' => 'Tuntas',
            'name_en' => 'Done',
            'description_id' => 'Selesaikan pelajaran secara bertahap.',
            'description_en' => 'Complete lessons step by step.',
            'tiers' => [
                'default' => 5,
                'bronze' => 25,
                'silver' => 100,
                'gold' => 400,
            ],
            'images' => [
                'default' => 'images/badges/tuntas/default.png',
                'bronze' => 'images/badges/tuntas/bronze.png',
                'silver' => 'images/badges/tuntas/silver.png',
                'gold' => 'images/badges/tuntas/gold.png',
            ],
        ],
        [
            'slug' => 'sekali-jalan',
            'name_id' => 'Sekali Jalan',
            'name_en' => 'One Shot',
            'description_id' => 'Lulus asesmen akhir di percobaan pertama dengan skor minimal 90.',
            'description_en' => 'Pass the final assessment on the first attempt with a score of at least 90.',
            'tiers' => [
                'default' => null,
            ],
            'images' => [
                'default' => 'images/badges/sekali-jalan/default.png',
            ],
        ],
        [
            'slug' => 'alumni',
            'name_id' => 'Alumni',
            'name_en' => 'Alumnus',
            'description_id' => 'Dapatkan minimal satu sertifikat.',
            'description_en' => 'Earn at least one certificate.',
            'tiers' => [
                'default' => null,
            ],
            'images' => [
                'default' => 'images/badges/alumni/default.png',
            ],
        ],
    ],

];
