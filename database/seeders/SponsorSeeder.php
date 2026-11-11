<?php

namespace Database\Seeders;

use App\Domain\Settings\Models\Sponsor;
use Illuminate\Database\Seeder;

class SponsorSeeder extends Seeder
{
    public function run(): void
    {
        $sponsors = [
            [
                'name' => 'Yayasan Islam Sejahtera',
                'url' => null,
                'description' => 'Mitra dalam pengembangan program pelatihan filantropi',
                'sort' => 1,
            ],
            [
                'name' => 'Kementerian Agama',
                'url' => null,
                'description' => 'Pendukung resmi program pelatihan',
                'sort' => 2,
            ],
            [
                'name' => 'Asosiasi Lembaga Zakat',
                'url' => null,
                'description' => 'Kolaborasi dalam pengembangan standar zakat',
                'sort' => 3,
            ],
            [
                'name' => 'Forum LSM Indonesia',
                'url' => null,
                'description' => 'Mitra dalam advokasi tata kelola organisasi',
                'sort' => 4,
            ],
        ];

        foreach ($sponsors as $sponsor) {
            Sponsor::firstOrCreate(
                ['name' => $sponsor['name']],
                $sponsor
            );
        }
    }
}
