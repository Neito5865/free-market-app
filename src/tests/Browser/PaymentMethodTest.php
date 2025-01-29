<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;

class PaymentMethodTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function test_updates_payment_method_display_immediately()
    {
        $this->browse(function (Browser $browser) {
            // ユーザー1を作成（購入者）
            $user1 = User::factory()->create([
                'name' => 'テストユーザー1',
                'email' => 'test1@test.com',
                'password' => bcrypt('password'),
            ]);

            // ユーザー2を作成（出品者）
            $user2 = User::factory()->create([
                'name' => 'テストユーザー2',
                'email' => 'test2@test.com',
                'password' => bcrypt('password'),
            ]);

            // テスト用の商品状態を作成
            $condition = Condition::create([
                'condition' => 'テストコンディション',
            ]);

            // 購入用のテスト商品を作成
            $item = Item::create([
                'name' => 'テスト商品',
                'price' => 1000,
                'description' => 'これはテスト用の商品です。',
                'user_id' => $user2->id,
                'condition_id' => $condition->id,
                'brand' => 'テストブランド',
                'image' => 'item-img/test.jpg'
            ]);

            $browser->loginAs($user1)
                ->visit(route('purchase.create', $item->id))
                ->assertSee('支払い方法')
                ->select('#payment-select', '1')
                ->pause(500)
                ->assertSeeIn('#selected-payment-method', 'コンビニ払い');
        });
    }
}
