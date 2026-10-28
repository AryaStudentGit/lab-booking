<?php

namespace Database\Seeders;

use App\Models\Laboratory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nim_nip' => '1000000001',
        ]);

        User::create([
            'name' => 'Budi Laboran',
            'email' => 'laboran@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'laboran',
            'nim_nip' => '1000000002',
        ]);

        User::create([
            'name' => 'Dr. Siti Dosen',
            'email' => 'dosen@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim_nip' => '1000000003',
        ]);

        User::create([
            'name' => 'Andi Mahasiswa',
            'email' => 'mahasiswa@univ.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nip' => '2023000001',
        ]);

        Laboratory::create([
            'name' => 'Lab Komputer Dasar',
            'description' => 'Laboratorium dilengkapi dengan 30 PC Intel Core i5 untuk praktikum dasar pemrograman.',
            'capacity' => 30,
            'is_active' => true,
        ]);

        Laboratory::create([
            'name' => 'Lab Jaringan & Keamanan',
            'description' => 'Laboratorium Cisco Router, Switch, dan server untuk simulasi jaringan.',
            'capacity' => 25,
            'is_active' => true,
        ]);
    }
}
