<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentsController;

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
    return redirect()->route('profile.create');
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
Route::get('/profile', function() {
    return view('auth.profile-create');
})->name('profile.create');

// トップページ
Route::get('/', [ItemsController::class, 'index'])->name('item.index');
// 商品詳細ページ
Route::get('item/{id}', [ItemsController::class, 'show'])->name('item.show');

// ログイン後
Route::middleware(['auth', 'verified.email'])->group(function() {
    Route::get('/mypage', [UsersController::class, 'show'])->name('user.show');

    Route::prefix('item/{id}')->group(function(){
        // いいね機能
        Route::post('favorite', [FavoriteController::class, 'store'])->name('favorite');
        Route::delete('unfavorite', [FavoriteController::class, 'destroy'])->name('unfavorite');

        // コメント機能
        Route::post('comment', [CommentsController::class, 'store'])->name('comment.store');
    });

});
