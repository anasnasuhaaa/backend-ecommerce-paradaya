<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('roles')->insert(
            [
                [
                    'id' => Str::uuid(),
                    'name' => 'admin'
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'user'
                ]
            ]
        );
    }
}
