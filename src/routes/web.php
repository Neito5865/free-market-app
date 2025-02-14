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
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

// メール認証関係
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);
    if (!$user) {
        return redirect()->route('login')->with('error', '認証対象のユーザーが見つかりません。');
    }

    if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
        Log::error('Invalid verification hash', ['id' => $id, 'hash' => $hash]);
        return redirect()->route('login')->with('error', '認証リンクが無効です。');
    }

    if ($user->hasVerifiedEmail()) {
        return redirect()->route('user.firstEdit')->with('info', 'すでにメール認証が完了しています。');
    }

    $user->email_verified_at = now();
    $user->save();

    Auth::login($user);

    return redirect()->route('user.firstEdit')->with('success', 'メール認証が完了しました。');
})->middleware(['signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['throttle:6,1'])->name('verification.send');

// 会員登録
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('store.register');

// トップページ
Route::get('/', [ItemsController::class, 'index'])->name('item.index');
// 商品詳細ページ
Route::get('item/{id}', [ItemsController::class, 'show'])->name('item.show');

// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle'])->name('stripe.webhook');

Route::middleware(['auth', 'verified.email'])->group(function() {
    Route::prefix('item/{id}')->group(function(){
        // お気に入り
        Route::post('favorite', [FavoriteController::class, 'store'])->name('favorite');
        Route::delete('unfavorite', [FavoriteController::class, 'destroy'])->name('unfavorite');

        // コメント
        Route::post('comment', [CommentsController::class, 'store'])->name('comment.store');
    });

    // 出品
    Route::prefix('sell')->group(function(){
        Route::get('', [SellProductController::class, 'create'])->name('item.create');
        Route::post('', [SellProductController::class, 'store'])->name('item.store');
    });

    // 新規登録後プロフィール設定
    Route::prefix('profile')->group(function(){
        Route::get('', [UsersController::class, 'firstEdit'])->name('user.firstEdit');
        Route::put('', [UsersController::class, 'firstUpdate'])->name('user.firstUpdate');
    });

    // マイページ
    Route::prefix('mypage')->group(function(){
        Route::get('', [UsersController::class, 'show'])->name('user.show');
        Route::get('/profile', [UsersController::class, 'edit'])->name('user.edit');
        Route::put('/profile', [UsersController::class, 'update'])->name('user.update');
    });

    // 購入
    Route::prefix('purchase/{id}')->group(function(){
        Route::get('', [PurchaseController::class, 'create'])->name('purchase.create');
        Route::post('payment', [PurchaseController::class, 'payment'])->name('purchase.payment');
        Route::get('completed', [PurchaseController::class, 'completed'])->name('purchase.completed');

        // 購入-送付先変更
        Route::prefix('address')->group(function(){
            Route::get('', [AddressesController::class, 'create'])->name('address.create');
            Route::post('', [AddressesController::class, 'store'])->name('address.store');
        });
    });
});
