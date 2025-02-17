<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sourcePath = base_path('public/item-img');

        $destinationPath = storage_path('app/public/item-img');

        $items = [
            [
                'user_id' => 1,
                'name' => '腕時計',
                'price' => 15000,
                'description' => 'スタイリッシュなデザインのメンズ腕時計',
                'image' => 'item-img/Armani+Mens+Clock.jpg',
                'brand' => 'テストブランドa',
                'condition_id' => 1,
            ],
            [
                'user_id' => 2,
                'name' => 'HDD',
                'price' => 5000,
                'description' => '高速で信頼性の高いハードディスク',
                'image' => 'item-img/HDD+Hard+Disk.jpg',
                'brand' => 'テストブランドb',
                'condition_id' => 2,
            ],
            [
                'user_id' => 3,
                'name' => '玉ねぎ3束',
                'price' => 300,
                'description' => '新鮮な玉ねぎ3束のセット',
                'image' => 'item-img/iLoveIMG+d.jpg',
                'brand' => 'テストブランドc',
                'condition_id' => 3,
            ],
            [
                'user_id' => 4,
                'name' => '革靴',
                'price' => 4000,
                'description' => 'クラシックなデザインの革靴',
                'image' => 'item-img/Leather+Shoes+Product+Photo.jpg',
                'brand' => 'テストブランドd',
                'condition_id' => 4,
            ],
            [
                'user_id' => 5,
                'name' => 'ノートPC',
                'price' => 45000,
                'description' => '高性能なノートパソコン',
                'image' => 'item-img/Living+Room+Laptop.jpg',
                'brand' => 'テストブランドe',
                'condition_id' => 1,
            ],
            [
                'user_id' => 6,
                'name' => 'マイク',
                'price' => 8000,
                'description' => '高音質のレコーディング用マイク',
                'image' => 'item-img/Music+Mic+4632231.jpg',
                'brand' => 'テストブランドf',
                'condition_id' => 2,
            ],
            [
                'user_id' => 7,
                'name' => 'ショルダーバッグ',
                'price' => 3500,
                'description' => 'おしゃれなショルダーバッグ',
                'image' => 'item-img/Purse+fashion+pocket.jpg',
                'brand' => 'テストブランドg',
                'condition_id' => 3,
            ],
            [
                'user_id' => 8,
                'name' => 'タンブラー',
                'price' => 500,
                'description' => '使いやすいタンブラー',
                'image' => 'item-img/Tumbler+souvenir.jpg',
                'brand' => 'テストブランドh',
                'condition_id' => 4,
            ],
            [
                'user_id' => 9,
                'name' => 'コーヒーミル',
                'price' => 4000,
                'description' => '手動のコーヒーミル',
                'image' => 'item-img/Waitress+with+Coffee+Grinder.jpg',
                'brand' => 'テストブランドi',
                'condition_id' => 1,
            ],
            [
                'user_id' => 10,
                'name' => 'メイクセット',
                'price' => 2500,
                'description' => '便利なメイクアップセット',
                'image' => 'item-img/Makeup+set.jpg',
                'brand' => 'テストブランドj',
                'condition_id' => 2,
            ],
        ];

        foreach ($items as $item) {
            $sourceFile = $sourcePath . '/' . basename($item['image']);
            $destinationFile = $destinationPath . '/' . basename($item['image']);

            if (!file_exists($destinationFile)) {
                @mkdir(dirname($destinationFile), 0777, true);
                copy($sourceFile, $destinationFile);
            }
        }

        DB::table('items')->insert($items);
    }
}
