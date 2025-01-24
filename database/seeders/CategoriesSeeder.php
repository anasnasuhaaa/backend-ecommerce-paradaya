<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('categories')->insert(
            [
                [
                    'id' => Str::uuid(),
                    'name' => 'wardah'
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'kahf'
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'emina'
                ],
                [
                    'id' => Str::uuid(),
                    'name' => 'omg'
                ]
            ]
        );
    }
}
