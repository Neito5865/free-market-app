<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Condition;
use Illuminate\Support\Facades\DB;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_user_is_register_comment()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
            'image' => 'profile-img/test_user1.jpg'
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ユーザー1をログイン状態に設定する
        $this->actingAs($user1);

        // コメントを登録する前の状態を確認
        $initialCommentCount = DB::table('comments')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(0, $initialCommentCount); // いいね数が0であることを確認

        // 必要なデータを準備
        $data = [
            'comment' => 'テストコメント',
        ];

        // コメントを登録
        $response = $this->post(route('comment.store', $item->id), $data);
        // リダイレクトしているか
        $response->assertStatus(302);

        // commentsテーブルにコメントが保存されているか
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user1->id,
            'comment' => 'テストコメント',
        ]);

        // コメントを登録した後の状態を確認
        $updatedCommentCount = DB::table('Comments')
            ->where('item_id', $item->id)
            ->count();
        $this->assertEquals(1, $updatedCommentCount);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        // コメントが表示されているか
        $response->assertSee('テストコメント');
        // コメント数がコメント登録後の状態になっているか
        $response->assertSee((string) $updatedCommentCount);
    }

    public function test_not_authenticated_is_not_register_comment()
    {
        // テストユーザーを作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // 必要なデータを準備
        $data = [
            'comment' => 'テストコメント',
        ];

        // コメントを登録
        $response = $this->post(route('comment.store', $item->id), $data);

        // ログインページにリダイレクトされるか
        $response->assertRedirect('/login');

        // commentsテーブルにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント',
        ]);
    }

    public function test_shows_validation_message_when_comment_is_missing()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ログイン状態に設定する
        $this->actingAs($user1);

        // コメントを登録
        $response = $this->post(
            route('comment.store', $item->id),
            ['comment' => ''],
            ['HTTP_REFERER' => route('item.show', $item->id) . '#comment-form']
        );

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['comment' => 'コメントを入力してください']);

        // リダイレクトされるか
        $response->assertRedirect(route('item.show', $item->id) . '#comment-form');
    }

    public function test_shows_validation_message_when_comment_is_more_than_256_characters()
    {
        // テストユーザー1を作成
        $user1 = User::factory()->create([
            'name' => 'テストユーザー1',
            'email' => 'test1@test.com',
            'password' => bcrypt('password'),
        ]);

        // テストユーザー2を作成
        $user2 = User::factory()->create([
            'name' => 'テストユーザー2',
            'email' => 'test2@test.com',
            'password' => bcrypt('password'),
        ]);

        // テスト用の商品状態を作成
        $condition = Condition::create([
            'condition' => 'テストコンディション',
        ]);

        // ユーザー2の出品商品を作成
        $item = Item::create([
            'name' => 'テスト商品',
            'price' => 1000,
            'description' => 'これはテスト用の商品です。',
            'user_id' => $user2->id,
            'condition_id' => $condition->id,
            'brand' => 'テストブランド',
            'image' => 'item-img/test.jpg'
        ]);

        // ログイン状態に設定する
        $this->actingAs($user1);

        // コメントを登録
        $response = $this->post(
            route('comment.store', $item->id),
            ['comment' => str_repeat('テストコメント', 37)],
            ['HTTP_REFERER' => route('item.show', $item->id) . '#comment-form']
        );

        // 検証：バリデーションメッセージが表示されるか
        $response->assertSessionHasErrors(['comment' => '255文字以内で入力してください']);

        // リダイレクトされるか
        $response->assertRedirect(route('item.show', $item->id) . '#comment-form');
    }
}
