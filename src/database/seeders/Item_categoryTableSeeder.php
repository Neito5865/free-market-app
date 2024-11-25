<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Item_categoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/public/csv/item_category.csv'), 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            DB::table('item_category')->insert([
                'item_id' => $data[1],
                'category_id' => $data[2],
            ]);
        }
        fclose($file);
    }
}
