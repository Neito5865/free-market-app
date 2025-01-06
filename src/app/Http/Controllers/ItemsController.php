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
        $keyword = $request->input('keyword');
        $items = collect();

        if ($page === 'recommend') {
            $itemsQuery = Item::with('purchase')->orderBy('id', 'desc');
            if (Auth::check()) {
                $itemsQuery->where('user_id', '!=', Auth::id());
            }
            if (!empty($keyword)) {
                $itemsQuery->where('name', 'LIKE', "%{$keyword}%");
            }
            $items = $itemsQuery->get();
        } elseif ($page === 'mylist') {
            if (Auth::check()) {
                $itemsQuery = Auth::user()->favorites()->withPivot('created_at')->orderBy('pivot_created_at', 'desc');
                if (!empty($keyword)) {
                    $itemsQuery->where('name', 'LIKE', "%{$keyword}%");
                }
                $items = $itemsQuery->get();
            }
        } else {
            $itemsQuery = Item::with('purchase')->orderBy('id', 'desc');
            if (!empty($keyword)) {
                $itemsQuery->where('name', 'LIKE', "%{$keyword}%");
            }
            $items = $itemsQuery->get();
        }

        return view('index', compact('page', 'items', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with('purchase')->find($id);
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
