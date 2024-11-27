<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page');

        if (!$page) {
            $page = Auth::check() ? 'mylist' : 'recommend';
        }

        if ($page === 'recommend') {
            $items = Item::orderBy('id', 'desc')->get();
        } elseif ($page === 'mylist') {
            if (!Auth::check()) {
                $items = collect();
            } else {
                $items = Auth::user()->favorites()->orderBy('id', 'desc')->get();
            }
        } else {
            $items = Item::orderBy('id', 'desc')->get();
        }

        return view('index', compact('page', 'items'));
    }

    public function show($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => 'ページを表示できません。'], 404);
        }
        $categories = $item->categories()->get();

        return view('items.show', compact('item', 'categories'));
    }
}
