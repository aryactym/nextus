<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Domain\Organization\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    /** Nama lembaga di-seed dengan tipe MPZ DD. */
    private const NAMES = [
        'KSPPS DARUL AMWAAL BAROKATUL ADZKIA (DAMBA)',
        'KSPPS BMT MENTARI UMAT WANGON',
        'KSPPS BMT AMANAH INDONESIA',
        'KSPPS BMT BINA INSAN MULIA (BIMA)',
        'KSPPS BMT AN NAJAH',
        'KSPPS BMT BAHTERA',
        'KSPS BMT HARAPAN UMMAT',
        'KSPPS DIRGANTARA MITRA SEJAHTERA',
        'KSPPS BMT PALUR',
        'KSPPS BMT KARIMA',
        'KSPPS BMT TUMANG',
        'KSPPS BMT INSAN MANDIRI',
        'KSPPS BMT ADISA REKSA MUAMALAT (ARMA)',
        'YAYASAN BALAI DAKWAH SRAGEN',
        'KUD BAHAGIA',
        'KSPPS KUD BAHAGIA',
        'YAYASAN HIDAYATURRAHMAN',
        'YAYASAN FATAHILLAH PENERANG UMAT KARANGANYAR',
        'YAYASAN NURUL IMAN RAWALO (YANURI)',
        'UPZ YAYASAN ALUMNI FK UNAND',
        'UPZ KAMPOENG SHOLAWAT',
        'KOMUNITAS PEDULI YATIM KOTA PARIAMAN',
        'BMT EL USWAH DHARMASRAYA',
        'YAYASAN PENDIDIKAN ISLAM AR RASYID',
        'MPZ EL MUMTAZAH SOLOK',
        'YAYASAN BERKAH PEDULI UMAT',
        'MPZ DKM ANNUR SARI ASIH SERANG',
        'MPZ YAYASAN MIFTAHUL FATAH (YAMIFA)',
        'MPZ ROHIS RS MATA ACHMAD WARDI',
        'MPZ YAYASAN PENDIDIKAN ISLAM HIDAYATUL MUBTADI\'IEN (YAPIHIM) BANTEN',
        'MPZ IKATAN ROHIS (IKRO) LEBAK',
        'MPZ YAYASAN MADINAH AL-HIJRAH',
        'MPZ LAZ AL-MADINAH',
        'MPZ MANARUL IMAN PEDULI',
        'MPZ GEMILANG',
        'MPZ MAKMUR SENTOSA',
        'YAYASAN RECEH PUN BISA',
        'LAZ DARUL FATTAH',
        'BAITUL MAAL MADANI CENTER',
        'IKA PLN JOGJA',
        'PT. BPRS BAROKAH DANA SEJAHTERA',
        'KYIBAR TANI MANDIRI',
        'BPRS MITRA AMAL MULIA',
        'BAITUL MAL MASJID UTSMAN BIN AFFAN',
        'UMMAT',
        'ARTHA BAROKAHA',
        'AROFAH MINA SEJAHTERA',
        'KSPPS BMT DANA INSANI',
        'ARTHA MANDIRI',
        'MANOREH SEJAHTERA',
        'SAHABAT BERAMAL BERSAMA',
        'KSPPS BMT PROSUMEN AMANAH MANDIRI',
        'SEMBADA CENDEKIA',
        'YAYASAN KAMPOENG ALQURAN',
        'YAYASAN PENDIDIKAN AMALIYAH',
        'PERMATA',
        'GRAB GOJEK FAMILY MEDAN (GGMF)',
        'YAYASAN AS-SHIDDIQ',
        'YAYASAN MAKKARUMPA',
        'YAYASAN NAJAMUDDIN SUKING',
        'YBM PLN UPMK V',
        'YAYASAN PANRITA INDONESIA PEDULI PANGKEP (YPIP)',
        'BKPRMI KOTA MAKASSAR',
        'PPKM SULSEL',
        'JOKER MAX MAKASSAR',
        'MASJID CHENG HOO TANJUNG BUNGA',
        'MASJID AR-RAHMAN',
        'BMT INSAN MANDIRI',
        'MASJID SHIROTAL MUSTAQIM',
        'YAYASAN MUTIARA BILQIS',
        'BMT INSAN MULIA',
        'KSPPS IBAADURRAHMAN SUKABUMI',
        'YAYASAN SEHATI GERAK BERSAMA',
        'KSPPS BAYTUL IKHTIAR BOGOR',
        'KSPPS BMT DANA UKHUWAH',
        'KSPPS BMT LARIBA ISLAMIC CENTRE',
        'KOPERASI JASA LKMS BMT TALAGA',
        'YAYASAN INDONESIA LEARNING QURAN',
        'YAYASAN GENERASI TANGGUH MULIA',
        'LAZ YANASHIRA',
        'YMAI',
        'BAZMA PERTAMINA',
        'LAZ PHR',
        'OKE OCE KEMANUSIAAN',
        'GERAK BARENG',
        'YAYASAN DARUSSALAM KOTA WISATA',
        'BMT AHSANA BERKAH SENTOSA',
        'YAYASAN PONPES DARUL HAMIDY',
        'YAYASAN BAHTERA MAJU INDONESIA',
        'MASJID AL-HIDAYAH MODERNLAND',
        'SAKU SAHABAT',
        'SAKU PEDULI',
        'MPZ KARYA SUARA DAN ASA',
        'MPZ RUMAH SINGGAH AIR BANGIS',
        'MPZIS DOA AMIN',
        'BMT AMANAH RIAU',
        'YAYASAN AMAL ISLAM KARIANGO',
        'YAYASAN FIRYAL INDONESIA',
    ];

    public function run(): void
    {
        $created = 0;

        foreach (self::NAMES as $name) {
            $name = Str::of($name)->trim()->upper()->value();
            $slug = Str::slug($name) ?: 'organisasi';
            $i = 2;

            while (Organization::withoutGlobalScopes()->where('slug', $slug)->exists()) {
                $slug = Str::slug($name).'-'.$i++;
            }

            Organization::create([
                'name' => ['id' => $name],
                'slug' => $slug,
                'type' => 'mpz_dd',
                'status' => 'active',
                'default_locale' => 'id',
            ]);
            $created++;
        }

        $this->command->info("OrganizationSeeder: {$created} organisasi (MPZ DD).");
    }
}
