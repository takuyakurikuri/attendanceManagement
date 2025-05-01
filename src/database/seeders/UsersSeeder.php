<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //管理権限者
        $param = [
            'name' => 'superuser',
            'email' => 'superuser@example.com',
            'password' => Hash::make('password'),
            'role' => 1,
        ];

        DB::table('users')->insert($param);

        User::factory()->count(9)->create();
    }
}