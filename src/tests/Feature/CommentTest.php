<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;
use App\Http\Middleware\VerifyCsrfToken;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_user_is_register_comment()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user1.jpg'
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

        $initialCommentCount = DB::table('comments')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $initialCommentCount);

        $data = [
            'comment' => 'テストコメント',
        ];
        $response = $this->post(route('comment.store', $item->id), $data);
        $response->assertStatus(302);

        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user1->id,
            'comment' => 'テストコメント',
        ]);

        $updatedCommentCount = DB::table('Comments')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $updatedCommentCount);

        $response = $this->get(route('item.show', $item->id));
        $response->assertSee('テストコメント');
        $response->assertSee((string) $updatedCommentCount);
    }

    public function test_not_authenticated_is_not_register_comment()
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        $data = [
            'comment' => 'テストコメント',
        ];
        $response = $this->post(route('comment.store', $item->id), $data);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    public function test_shows_validation_message_when_comment_is_missing()
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

        $response = $this->post(
            route('comment.store', $item->id),
            ['comment' => ''],
            ['HTTP_REFERER' => route('item.show', $item->id) . '#comment-form']
        );

        $response->assertSessionHasErrors(['comment' => 'コメントを入力してください']);
        $response->assertRedirect(route('item.show', $item->id) . '#comment-form');
    }

    public function test_shows_validation_message_when_comment_is_more_than_256_characters()
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
        $response = $this->post(
            route('comment.store', $item->id),
            ['comment' => str_repeat('テストコメント', 37)],
            ['HTTP_REFERER' => route('item.show', $item->id) . '#comment-form']
        );

        $response->assertSessionHasErrors(['comment' => '255文字以内で入力してください']);

        $response->assertRedirect(route('item.show', $item->id) . '#comment-form');
    }
}
