<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemsController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommend');

        if ($page === 'recommend') {
            $itemsQuery = Item::orderBy('id', 'desc');
            if (Auth::check()) {
                $itemsQuery->where('user_id', '!=', Auth::id());
            }
            $items = $itemsQuery->get();
        } elseif ($page === 'mylist') {
            if (Auth::check()) {
                $items = Auth::user()->favorites()->withPivot('created_at')->orderBy('pivot_created_at', 'desc')->get();
            } else {
                $items = collect();
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
        $countFavorites = $item->favoriteUsers()->count();
        $comments = $item->comments()->orderBy('id', 'asc')->get();
        $countComments = $item->comments()->count();

        return view('items.show', compact('item', 'categories', 'countFavorites', 'comments', 'countComments'));
    }
}
