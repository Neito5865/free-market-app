<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\Condition;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;

class SellProductController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $user_id = Auth::id();
        $itemData = $request->only([
            'name',
            'price',
            'description',
            'brand',
            'condition_id',
        ]);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('item-img', 'public');
            $itemData['image'] = 'item-img/' . basename($path);
        }
        $itemData['user_id'] = $user_id;
        $item = Item::create($itemData);
        $item->categories()->attach($request->input('categories'));

        return redirect()->route('user.show')->with('success', '商品を出品しました');
    }
}
