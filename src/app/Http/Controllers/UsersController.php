<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;
use App\Http\Requests\ProfileRequest;

class UsersController extends Controller
{
    public function firstEdit()
    {
        $user = Auth::user();
        return view('auth.profile-create', compact('user'));
    }

    public function firstUpdate(ProfileRequest $request)
    {
        $user = Auth::user();
        $userData = $request->only([
            'name',
            'postCode',
            'address',
            'building',
        ]);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile-img', 'public');
            $userData['image'] = 'profile-img/' . basename($path);
        }
        $user->update($userData);
        return redirect()->route('item.index');
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

        return view('users.show', compact('tab', 'items', 'user'));
    }

    public function edit()
    {
        $user = Auth::user();
        return view('users.edit', compact('user'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();
        $userData = $request->only([
            'name',
            'postCode',
            'address',
            'building',
        ]);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile-img', 'public');
            $userData['image'] = 'profile-img/' . basename($path);
        }
        $user->update($userData);
        return redirect()->route('user.show')->with('successMessage', 'プロフィールを更新しました');
    }
}
