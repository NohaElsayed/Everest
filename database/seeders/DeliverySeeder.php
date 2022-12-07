<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class DeliverySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('employs')->insert([
            'id' => 1,
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'photo' => 'def.png',
            'password' => bcrypt(12345678),
        ]);
        DB::table('employs')->insert([
            'id' => 2,
            'name' => 'Manager',
            'email' => 'admin@gmail.com',
            'photo' => 'def.png',
            'password' => bcrypt(12345678),
        ]);

    }
}
