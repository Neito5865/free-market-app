<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condition;
use App\Models\Category;

class SellProductController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $conditions = Condition::all();
        return view('items.create', compact('categories', 'conditions'));
    }

    public function store(Request $request)
    {
        // $item->categories()->attach($request->input('categories'));
    }
}
