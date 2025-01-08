<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Item_categoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['item_id' =>1, 'category_id' => 1],
            ['item_id' =>1, 'category_id' => 5],
            ['item_id' =>2, 'category_id' => 2],
            ['item_id' =>3, 'category_id' => 10],
            ['item_id' =>4, 'category_id' => 1],
            ['item_id' =>4, 'category_id' => 5],
            ['item_id' =>5, 'category_id' => 2],
            ['item_id' =>6, 'category_id' => 2],
            ['item_id' =>7, 'category_id' => 1],
            ['item_id' =>7, 'category_id' => 4],
            ['item_id' =>8, 'category_id' => 10],
            ['item_id' =>9, 'category_id' => 10],
            ['item_id' =>10, 'category_id' => 4],
            ['item_id' =>10, 'category_id' => 6],
        ];

        $now = Carbon::now();

        $data = array_map(function($row) use ($now) {
            return array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $data);

        DB::table('item_category')->insert($data);
    }
}
