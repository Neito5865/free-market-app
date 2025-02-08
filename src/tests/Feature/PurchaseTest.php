<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Condition;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Address;
use Mockery;
use Stripe\Checkout\Session as StripeSession;
use App\Http\Middleware\VerifyCsrfToken;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_function()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Stripeのモックを設定
        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        // テストユーザー1（購入者）を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        // テストユーザー2（出品者）を作成
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

        // 購入画面へアクセス
        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        // 商品購入のリクエストを送信
        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        // モックしたリダイレクトURLが使われているか確認
        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        // 購入完了後にアクセス
        $response = $this->get(route('purchase.completed', $item->id));

        // トップページへのリダイレクトを確認
        $response->assertRedirect(route('item.index'));

        // purchasesテーブルにデータが保存されたか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
        ]);

        // Addressesテーブルにデータが保存されたか確認
        $this->assertDatabaseHas('addresses', [
            'name' => $user1->name,
            'post_code' => $user1->post_code,
            'address' => $user1->address,
            'building' => $user1->building,
        ]);

        // フラッシュメッセージの確認
        $response->assertSessionHas('successMessage', '購入が完了しました');
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Stripeのモックを設定
        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        // テストユーザー1（購入者）を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        // テストユーザー2（出品者）を作成
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

        // 購入画面へアクセス
        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        // 商品購入のリクエストを送信
        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        // モックしたリダイレクトURLが使われているか確認
        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        // 購入完了後のリクエスト（リダイレクト）
        $response = $this->get(route('purchase.completed', $item->id));
        $response->assertRedirect(route('item.index'));

        // 商品一覧ページへアクセス
        $response = $this->get(route('item.index'));

        // 購入済み商品が「SOLD」と表示されることを確認
        $response->assertSee('SOLD');
    }

    public function test_purchased_items_add_list_on_mypage()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        // Stripeのモックを設定
        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        // テストユーザー1（購入者）を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        // テストユーザー2（出品者）を作成
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

        // 購入画面へアクセス
        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        // 商品購入のリクエストを送信
        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        // モックしたリダイレクトURLが使われているか確認
        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        // 購入完了後のリクエスト（リダイレクト）
        $response = $this->get(route('purchase.completed', $item->id));
        $response->assertRedirect(route('item.index'));

        // マイページの購入した商品一覧へアクセスする
        $response = $this->get(route('user.show', ['tab' => 'buy']));

        // 購入済み商品が表示されることを確認
        $response->assertSee($item->name);
    }
}
