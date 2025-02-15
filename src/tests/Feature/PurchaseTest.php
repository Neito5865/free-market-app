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

        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        $response = $this->get(route('purchase.completed', $item->id));

        $response->assertRedirect(route('item.index'));

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
        ]);

        $this->assertDatabaseHas('addresses', [
            'name' => $user1->name,
            'post_code' => $user1->post_code,
            'address' => $user1->address,
            'building' => $user1->building,
        ]);

        $response->assertSessionHas('success', '購入が完了しました');
    }

    public function test_purchased_items_are_marked_as_sold()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        $response = $this->get(route('purchase.completed', $item->id));
        $response->assertRedirect(route('item.index'));

        $response = $this->get(route('item.index'));
        $response->assertSee('SOLD');
    }

    public function test_purchased_items_add_list_on_mypage()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $stripeSessionMock = Mockery::mock('alias:Stripe\Checkout\Session');
        $stripeSessionMock
            ->shouldReceive('create')
            ->once()
            ->andReturn((object)[
                'url' => 'https://checkout.stripe.com/mock-session'
            ]);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $this->actingAs($user1);

        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => $user1->name,
                'post_code' => $user1->post_code,
                'address' => $user1->address,
                'building' => $user1->building,
            ],
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        $response = $this->get(route('purchase.completed', $item->id));
        $response->assertRedirect(route('item.index'));

        $response = $this->get(route('user.show', ['tab' => 'buy']));

        $response->assertSee($item->name);
    }
}
