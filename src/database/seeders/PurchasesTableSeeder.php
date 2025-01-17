<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PurchasesTableSeeder extends Seeder
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
                'user_id' => $i,
                'item_id' => $i + 1,
                'address_id' => $i,
                'payment_method' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('purchases')->insert($param);
        }
    }
}
