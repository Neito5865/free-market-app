<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ItemsController;
use App\Http\Controllers\UsersController;

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
})->name('verification.notice');
// メール認証リンクを検証
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('profile.create');
})->middleware(['auth', 'signed'])->name('verification.verify');
// メール認証リンクを再送
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', '認証リンクが送信されました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 会員登録
Route::get('/register', function () {
    return view('auth.register');
})->name('register');
Route::post('/register', [RegisterController::class, 'create'])->name('create.register');
Route::get('/profile', function(){
    return view('auth.profile-create');
})->name('profile.create');

// トップページ
Route::get('/', [ItemsController::class, 'index'])->name('item.index');
// 商品詳細ページ
Route::get('item/{id}', [ItemsController::class, 'show'])->name('item.show');

// ログイン後
Route::middleware(['auth', 'verified'])->group(function(){
    //
});
