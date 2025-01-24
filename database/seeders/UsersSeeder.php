<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $adminRole = DB::table('roles')->where('name', 'admin')->value('id');
        $userRole = DB::table('roles')->where('name', 'user')->value('id');
        DB::table('users')->insert(
            [
                [
                    'id' => Str::uuid(),
                    'name' => 'user',
                    'email' => 'user@gmail.com',
                    'password' => Hash::make('password'),
                    'role_id' => $userRole
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'admin',
                    'email' => 'admin@gmail.com',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole
                ],
            ]
        );
    }
}
