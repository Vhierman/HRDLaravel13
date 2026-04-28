<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('users')->insert([
            'nik' => '5678',
            'name' => 'Olay',
            'roles' => 'karyawan',
            'email' => 'olay@olay.com',
            'password' => Hash::make('password'),
            'input_oleh' => 'Olay',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
