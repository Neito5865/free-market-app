<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 1; $i <=3; $i++) {
            $param = [
                'name' => '送付先ユーザー' . $i,
                'post_code' => '123-4567',
                'address' => 'テスト県テスト市テスト' . $i . '-' . $i . '-' . $i,
                'building' => 'マンション' . $i,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('addresses')->insert($param);
        }
    }
}
