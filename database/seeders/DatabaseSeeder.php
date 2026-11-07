<?php

namespace Database\Seeders;

use App\Domain\Organization\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Organisasi default untuk MVP
        $org = Organization::create([
            'name' => 'Yayasan Filantropi Islam',
            'slug' => 'yayasan-filantropi-islam',
            'default_locale' => 'id',
            'status' => 'active',
        ]);

        // Peran sistem
        $roles = [
            'Super Admin' => 'Pengaturan sistem dan semua organisasi',
            'Admin' => 'Kelola user, kategori, harga, template sertifikat, transaksi, laporan',
            'Instructor' => 'Kelola kursus, modul, materi, kuis; lihat progres peserta',
            'Learner' => 'Daftar, belajar, kuis, unduh sertifikat, donasi/bayar, riwayat transaksi',
            'Guest' => 'Lihat katalog, preview kursus, ganti bahasa, verifikasi sertifikat',
        ];

        foreach ($roles as $name => $description) {
            Role::firstOrCreate(['name' => $name, 'guard_name' => 'web'])
                ->description = $description;
        }

        // Super Admin awal
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $org->id,
            'locale' => 'id',
        ]);
        $admin->assignRole('Super Admin');

        $this->call(PermissionSeeder::class);
        $this->call(PendidikanSeeder::class);
        $this->call(PageSeeder::class);
    }
}
