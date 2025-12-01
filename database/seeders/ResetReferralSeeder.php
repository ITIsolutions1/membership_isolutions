<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;

class ResetReferralSeeder extends Seeder
{
    public function run(): void
    {
        // Update hanya yang punya referral
        Anggota::whereNotNull('referred_by')
            ->update(['referred_by' => null]);

        echo "Referral pada semua anggota berhasil direset menjadi NULL.\n";
    }
}
