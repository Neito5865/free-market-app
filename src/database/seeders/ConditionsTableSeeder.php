<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConditionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        $now = Carbon::now();

        $data = array_map(function($condition) use ($now) {
            return [
                'condition' => $condition,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }, $conditions);

        DB::table('conditions')->insert($data);
    }
}
