<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\SellProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\AddressesController;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// メール認証通知を表示
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
// メール認証リンクを検証
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('user.firstEdit');
})->middleware(['auth', 'signed'])->name('verification.verify');
// メール認証リンクを再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 会員登録
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'create'])->name('create.register');

// トップページ
Route::get('/', [ItemsController::class, 'index'])->name('item.index');
// 商品詳細ページ
Route::get('item/{id}', [ItemsController::class, 'show'])->name('item.show');

// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

// ログイン後
Route::middleware(['auth', 'verified.email'])->group(function() {
    Route::prefix('item/{id}')->group(function(){
        // いいね機能
        Route::post('favorite', [FavoriteController::class, 'store'])->name('favorite');
        Route::delete('unfavorite', [FavoriteController::class, 'destroy'])->name('unfavorite');

        // コメント送信機能
        Route::post('comment', [CommentsController::class, 'store'])->name('comment.store');
    });

    // 出品
    Route::prefix('sell')->group(function(){
        // 出品画面の表示
        Route::get('', [SellProductController::class, 'create'])->name('item.create');
        // 出品機能
        Route::post('', [SellProductController::class, 'store'])->name('item.store');
    });

    // マイページ関係
    // ログイン後のプロフィール設定
    Route::prefix('profile')->group(function(){
        Route::get('', [UsersController::class, 'firstEdit'])->name('user.firstEdit');
        Route::put('', [UsersController::class, 'firstUpdate'])->name('user.firstUpdate');
    });
    Route::prefix('mypage')->group(function(){
        // マイページ画面
        Route::get('', [UsersController::class, 'show'])->name('user.show');
        // マイページ編集画面
        Route::get('/profile', [UsersController::class, 'edit'])->name('user.edit');
        // マイページ編集処理
        Route::put('/profile', [UsersController::class, 'update'])->name('user.update');
    });

    // 購入関係
    Route::prefix('purchase')->group(function(){
        // 購入画面の表示
        Route::get('{id}', [PurchaseController::class, 'create'])->name('purchase.create');
        // 送付先変更画面の表示
        Route::get('address/{id}', [AddressesController::class, 'create'])->name('address.create');
        // 送付先変更の入力情報を保存
        Route::post('address/{id}', [AddressesController::class, 'store'])->name('address.store');
        // 購入の処理
        Route::post('payment/{id}', [PurchaseController::class, 'payment'])->name('purchase.payment');
        // 購入後のページ
        Route::get('completed/{id}', [PurchaseController::class, 'completed'])->name('purchase.completed');
    });
});
