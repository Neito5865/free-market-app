<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/public/csv/categories.csv'), 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            Category::create([
                'category' => $data[1]
            ]);
        }
        fclose($file);
    }
}
