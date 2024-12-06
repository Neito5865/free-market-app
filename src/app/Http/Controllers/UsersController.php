<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;

class UsersController extends Controller
{
    public function create()
    {
        return view('auth.profile-create');
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $tab = $request->query('tab', 'sell');

        if ($tab === 'sell') {
            $items = $user->items()->orderBy('id', 'desc')->get();
        } elseif ($tab === 'buy') {
            // ユーザーが購入した商品を取得
        } else {
            $items = $user->items()->orderBy('id', 'desc')->get();
        }

        return view('users.show', compact('tab', 'items'));
    }
}
