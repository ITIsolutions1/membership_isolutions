<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;

class UpdateAnggotaPoinKoinSeeder extends Seeder
{
    public function run(): void
    {
        // Update semua anggota yang poin & koin masih 0
        Anggota::where('poin', 0)
            ->where('koin', 0)
            ->update([
                'poin' => 1,
                'koin' => 1000
            ]);

        echo "Seeder selesai! Anggota dengan poin 0 & koin 0 sudah diupdate.\n";
    }
}
