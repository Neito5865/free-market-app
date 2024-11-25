<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/public/csv/items.csv'), 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            Item::create([
                'user_id' => $data[1],
                'name' => $data[2],
                'price' => $data[3],
                'description' => $data[4],
                'image' => $data[5],
                'condition_id' => $data[6],
            ]);
        }
        fclose($file);
    }
}
