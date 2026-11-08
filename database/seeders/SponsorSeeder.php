<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Catalog\Models\Sponsor;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'Yayasan Islam Sejahtera',
                'slug' => 'yayasan-islam-sejahtera',
                'logo_url' => null,
                'website_url' => null,
                'description' => 'Mitra dalam pengembangan program pelatihan filantropi',
                'sort' => 1,
            ],
            [
                'name' => 'Kementerian Agama',
                'slug' => 'kementerian-agama',
                'logo_url' => null,
                'website_url' => null,
                'description' => 'Pendukung resmi program pelatihan',
                'sort' => 2,
            ],
            [
                'name' => 'Asosiasi Lembaga Zakat',
                'slug' => 'asosiasi-lembaga-zakat',
                'logo_url' => null,
                'website_url' => null,
                'description' => 'Kolaborasi dalam pengembangan standar zakat',
                'sort' => 3,
            ],
            [
                'name' => 'Forum LSM Indonesia',
                'slug' => 'forum-lsm-indonesia',
                'logo_url' => null,
                'website_url' => null,
                'description' => 'Mitra dalam advokasi tata kelola organisasi',
                'sort' => 4,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::firstOrCreate(
                ['slug' => $sponsor['slug']],
                $sponsor
            );
        }
    }
}
