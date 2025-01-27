<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Mockery;
use Stripe\Checkout\Session as StripeSession;

class EditAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_is_update_on_purchase_display()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成（商品出品のため）
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

        // ユーザー1をログイン状態に設定
        $this->actingAs($user1);

        // 購入画面にアクセス
        $response = $this->get(route('purchase.create', $item->id));

        // 現在、送付先が設定されていないことを確認
        $response->assertSee('配送先が未設定または不完全です。');

        // 送付先変更画面にアクセス
        $response = $this->get(route('address.create', $item->id));
        $response->assertStatus(200);

        // 送付先のリクエストを送信
        $response = $this->post(route('address.store', $item->id), [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        // リダイレクト確認（購入画面にリダイレクト）
        $response->assertRedirect(route('purchase.create', $item->id));

        // セッションに保存されているか確認
        $this->assertEquals('テスト宛名', session('selected_address.name'));
        $this->assertEquals('111-1111', session('selected_address.post_code'));
        $this->assertEquals('テスト県テスト市テスト区テスト1-1-1', session('selected_address.address'));
        $this->assertEquals('テストマンション', session('selected_address.building'));

        // 購入画面へ再度アクセス
        $response = $this->get(route('purchase.create', $item->id));
        // 送付先が登録した情報に反映されていることを確認
        $response->assertSeeInOrder([
            'テスト宛名',
            '111-1111',
            'テスト県テスト市テスト区テスト1-1-1',
            'テストマンション'
        ]);
    }

    public function test_purchased_item_and_address_are_linked()
    {
        // Stripeのモックを設定
        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        // テストユーザー1を作成（購入者）
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成（出品者）
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

        // ユーザー1をログイン状態に設定
        $this->actingAs($user1);

        // 送付先変更画面にアクセス
        $response = $this->get(route('address.create', $item->id));
        $response->assertStatus(200);

        // 送付先のリクエストを送信
        $response = $this->post(route('address.store', $item->id), [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);
        $response->assertRedirect(route('purchase.create', $item->id));

        // 購入画面へアクセス
        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        // 商品購入のリクエストを送信
        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => 'テスト宛名',
                'post_code' => '111-1111',
                'address' => 'テスト県テスト市テスト区テスト1-1-1',
                'building' => 'テストマンション',
            ],
        ]);

        // モックしたリダイレクトURLが使われているか確認
        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        // 購入完了後にアクセス
        $response = $this->get(route('purchase.completed', $item->id));

        // トップページへのリダイレクトを確認
        $response->assertRedirect(route('item.index'));

        // 購入情報がデータベースに保存されているか確認
        $this->assertDatabaseHas('addresses', [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション',
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
            // 購入時に登録した送付先住所が保存されているか
            'address_id' => Address::where('name', 'テスト宛名')->first()->id,
            'payment_method' => 2, // クレジットカード
        ]);
    }
}
