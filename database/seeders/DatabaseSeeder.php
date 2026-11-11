<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Organisasi default — aman dijalankan berulang
        $org = Organization::firstOrCreate(
            ['slug' => 'yayasan-filantropi-islam'],
            [
                'name' => ['id' => 'Yayasan Filantropi Islam'],
                'default_locale' => 'id',
                'status' => 'active',
            ]
        );

        // Peran sistem
        $roles = [
            'Super Admin' => 'Pengaturan sistem dan semua organisasi',
            'Admin' => 'Kelola user, kategori, harga, template sertifikat, transaksi, laporan',
            'Org Admin' => 'Kelola peserta dan lihat laporan capaian organisasi sendiri',
            'Instructor' => 'Kelola kursus, modul, materi, kuis; lihat progres peserta',
            'Learner' => 'Daftar, belajar, kuis, unduh sertifikat, donasi/bayar, riwayat transaksi',
            'Guest' => 'Lihat katalog, preview kursus, ganti bahasa, verifikasi sertifikat',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // Super Admin awal — skip bila sudah ada
        $admin = User::withoutGlobalScopes()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'organization_id' => $org->id,
                'locale' => 'id',
            ]
        );

        if (! $admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }

        $this->call(PermissionSeeder::class);
        $this->call(PendidikanSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(SponsorSeeder::class);
        $this->call(DemoSeeder::class);
        $this->call(MpzSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(OrganizationSeeder::class);
    }
}
