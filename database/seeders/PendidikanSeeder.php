<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Catalog\Models\Pendidikan;
use Illuminate\Database\Seeder;

class PendidikanSeeder extends Seeder
{
    public function run(): void
    {
        $pendidikans = [
            ['name' => 'SD / Sederajat', 'level' => 1],
            ['name' => 'SMP / Sederajat', 'level' => 2],
            ['name' => 'SMA / Sederajat', 'level' => 3],
            ['name' => 'Diploma / D3', 'level' => 4],
            ['name' => 'Diploma / D4', 'level' => 5],
            ['name' => 'Sarjana / S1', 'level' => 6],
            ['name' => 'Magister / S2', 'level' => 7],
            ['name' => 'Doktor / S3', 'level' => 8],
        ];

        foreach ($pendidikans as $pendidikan) {
            Pendidikan::firstOrCreate(['name' => $pendidikan['name']], $pendidikan);
        }
    }
}
