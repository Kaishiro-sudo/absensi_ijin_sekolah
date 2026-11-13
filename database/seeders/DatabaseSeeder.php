<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Panggil UserSeeder yang sudah kita buat
        $this->call([
            UserSeeder::class,
        ]);
    }
}
