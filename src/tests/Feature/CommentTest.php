<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use Illuminate\Support\Facades\DB;

class CommentTest extends TestCase
{
    // テスト実行前に以下を実行する必要あり
    // php artisan migrate:fresh --env=testing
    // php artisan db:seed --env=testing

    public function test_login_user_is_register_comment()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=5の商品を取得
        $item = Item::find(5);

        // ログイン状態に設定する
        $this->actingAs($user);

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
            'user_id' => $user->id,
            'comment' => 'テストコメント',
        ]);

        // 商品詳細ページへアクセス
        $response = $this->get(route('item.show', $item->id));
        // コメントが表示されているか
        $response->assertSee('テストコメント');
    }

    public function test_not_authenticated_is_not_register_comment()
    {
        // item_id=5の商品を取得
        $item = Item::find(5);

        // 必要なデータを準備
        $data = [
            'comment' => 'テストコメント2',
        ];

        // コメントを登録
        $response = $this->post(route('comment.store', $item->id), $data);

        // ログインページにリダイレクトされるか
        $response->assertRedirect('/login');

        // commentsテーブルにコメントが保存されていないことを確認
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'comment' => 'テストコメント2',
        ]);
    }

    public function test_shows_validation_message_when_comment_is_missing()
    {
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=5の商品を取得
        $item = Item::find(5);

        // ログイン状態に設定する
        $this->actingAs($user);

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
        // user_id=1のユーザーを取得
        $user = User::find(1);

        // item_id=5の商品を取得
        $item = Item::find(5);

        // ログイン状態に設定する
        $this->actingAs($user);

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
