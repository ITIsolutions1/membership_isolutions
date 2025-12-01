<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResetPoinKoinSeeder extends Seeder
{
    public function run()
    {
        DB::table('anggotas')->update([
            'poin' => 0,
            'koin' => 0,
        ]);
    }
}
