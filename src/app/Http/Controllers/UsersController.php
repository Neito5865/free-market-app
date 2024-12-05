<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class UsersController extends Controller
{
    public function show(Request $request)
    {
        $tab = $request->query('tag', 'exhibition');

        if ($tab === 'exhibition') {
            $itemsQuery = Item::orderBy('id', 'desc');
            $items = $itemsQuery->get();
        } elseif ($tab === 'purchase') {
            // ログインユーザーが購入した商品一覧を取得
        } else {
            $items = Item::orderBy('id', 'desc')->get();
        }

        return view('users.show', compact('tab', 'items'));
    }
}
