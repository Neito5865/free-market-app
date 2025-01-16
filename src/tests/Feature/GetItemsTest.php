<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Address;
use App\Models\Purchase;

class GetItemsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // テスト用データベースをリフレッシュしてシーディング
        $this->artisan('db:seed --env=testing');
    }

    public function test_get_all_items()
    {
        // 商品一覧ページへアクセス
        $response = $this->get('/');
        $response->assertStatus(200);

        // 商品データがレスポンスに含まれることを確認
        $items = Item::all();
        foreach ($items as $item) {
            $response->assertSee($item->name);
        }
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        // 送付先データを作成
        $address = Address::factory()->create([
            'name' => 'テストユーザー',
            'post_code' => '123-4567',
            'address' => '東京都テスト区テスト',
            'building' => 'テスト',
        ]);

        // 購入情報を保存
        Purchase::factory()->create([
            'user_id' => User::first()->id,
            'item_id' => Item::find(2)->id,
            'address_id' => $address->id,
            'payment_method' => 1,
        ]);

        // 商品一覧ページにアクセス
        $response = $this->get('/');

        // 購入済み商品が「Sold」と表示されることを確認
        $response->assertSee('Sold');

        // 商品名と一緒に「Sold」が表示されているか
        $response->assertSee('HDD');
    }
}
