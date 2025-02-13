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
use App\Http\Middleware\VerifyCsrfToken;

class EditAddressTest extends TestCase
{
    use RefreshDatabase;

    public function test_address_is_update_on_purchase_display()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
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

        $response->assertSee('配送先が未設定または不完全です。');

        $response = $this->get(route('address.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('address.store', $item->id), [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);

        $response->assertRedirect(route('purchase.create', $item->id));

        $this->assertEquals('テスト宛名', session('selected_address.name'));
        $this->assertEquals('111-1111', session('selected_address.post_code'));
        $this->assertEquals('テスト県テスト市テスト区テスト1-1-1', session('selected_address.address'));
        $this->assertEquals('テストマンション', session('selected_address.building'));

        $response = $this->get(route('purchase.create', $item->id));

        $response->assertSeeInOrder([
            'テスト宛名',
            '111-1111',
            'テスト県テスト市テスト区テスト1-1-1',
            'テストマンション'
        ]);
    }

    public function test_purchased_item_and_address_are_linked()
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

        $response = $this->get(route('address.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('address.store', $item->id), [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション'
        ]);
        $response->assertRedirect(route('purchase.create', $item->id));

        $response = $this->get(route('purchase.create', $item->id));
        $response->assertStatus(200);

        $response = $this->post(route('purchase.payment', $item->id), [
            'payment_method' => 2,
            'selected_address' => [
                'name' => 'テスト宛名',
                'post_code' => '111-1111',
                'address' => 'テスト県テスト市テスト区テスト1-1-1',
                'building' => 'テストマンション',
            ],
        ]);

        $response->assertRedirectContains('https://checkout.stripe.com/mock-session');

        $response = $this->get(route('purchase.completed', $item->id));

        $response->assertRedirect(route('item.index'));

        $this->assertDatabaseHas('addresses', [
            'name' => 'テスト宛名',
            'post_code' => '111-1111',
            'address' => 'テスト県テスト市テスト区テスト1-1-1',
            'building' => 'テストマンション',
        ]);

        $this->assertDatabaseHas('purchases', [
            'user_id' => $user1->id,
            'item_id' => $item->id,
            'address_id' => Address::where('name', 'テスト宛名')->first()->id,
            'payment_method' => 2,
        ]);
    }
}
