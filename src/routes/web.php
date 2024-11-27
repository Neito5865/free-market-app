<?php

use Illuminate\Support\Facades\Route;
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

// トップページ
Route::get('/', [ItemsController::class, 'index'])->name('item.index');
// 商品詳細ページ
Route::get('item/{id}', [ItemsController::class, 'show'])->name('item.show');

// 会員登録・ログイン・プロフィール（仮）
Route::get('register', [UsersController::class, 'register']);
Route::get('login', [UsersController::class, 'login']);
Route::get('profile', [UsersController::class, 'profile']);
