<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Condition;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $file = fopen(storage_path('app/public/csv/conditions.csv'), 'r');

        while (($data = fgetcsv($file)) !== FALSE) {
            Condition::create([
                'condition' => $data[1]
            ]);
        }
        fclose($file);
    }
}
