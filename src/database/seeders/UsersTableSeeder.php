<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <=10; $i++) {
            $param = [
                'name' => 'ユーザー' . $i,
                'email' => 'user' . $i . '@example.com',
                'email_verified_at' => Carbon::now(),
                'password' => Hash::make('password'),
                'postCode' => '999-9999',
                'address' => 'テスト県テスト市テスト' . $i . '-' . $i . '-' . $i,
                'building' => 'テストマンション' . $i,
                'image' => 'profile-img/person-default.png',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('users')->insert($param);
        }
    }
}
