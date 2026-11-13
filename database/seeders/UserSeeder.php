<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Siswa
        User::create([
            'name' => 'Mahadma Siswa',
            'email' => 'siswa@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
        ]);

        // 2. Akun Guru bk
        User::create([
            'name' => 'Ibu Guru bk',
            'email' => 'bk@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'guru_bk',
        ]);

        // 3. Akun Satpam
        User::create([
            'name' => 'Bapak Satpam Gerbang',
            'email' => 'satpam@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'satpam',
        ]);
    }
}
