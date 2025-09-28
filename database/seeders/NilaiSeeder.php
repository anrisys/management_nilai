<?php

namespace Database\Seeders;

use App\Models\Nilai;
use App\Models\Siswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NilaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua siswa
        $siswas = Siswa::all();

        // Mapel yang tersedia
        $mapels = ['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'IPA', 'IPS'];

        foreach ($siswas as $siswa) {
            foreach ($mapels as $mapel) {
                Nilai::create([
                    'siswa_id' => $siswa->id,
                    'kelas' => $siswa->kelas,
                    'mapel' => $mapel,
                    'nilai' => rand(60, 95), // Nilai antara 60-95
                ]);
            }
        }

        $this->command->info('Data nilai berhasil ditambahkan untuk ' . $siswas->count() . ' siswa!');
    }
}
