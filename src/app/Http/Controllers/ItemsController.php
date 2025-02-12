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
        $items = $this->getFilteredItems($page, $keyword);

        return view('index', compact('page', 'items', 'keyword'));
    }

    private function getFilteredItems($page, $keyword)
    {
        switch ($page) {
            case 'recommend':
                return $this->getRecommendItems($keyword);
            case 'mylist':
                return $this->getMyListItems($keyword);
            default:
                return $this->getAllItems($keyword);
        }
    }

    private function getRecommendItems($keyword)
    {
        $query = Item::with('purchase')->orderBy('id', 'desc');

        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        return $this->applyKeywordFilter($query, $keyword)->get();
    }

    private function getMylistItems($keyword)
    {
        if (!Auth::check()) {
            return collect();
        }

        $query = Auth::user()->favorites()
            ->where('items.user_id', '!=', Auth::id())
            ->withPivot('created_at')
            ->orderBy('pivot_created_at', 'desc');

            return $this->applyKeywordFilter($query, $keyword)->get();
    }

    private function getAllItems($keyword)
    {
        $query = Item::with('purchase')->orderBy('id', 'desc');
        return $this->applyKeywordFilter($query, $keyword)->get();
    }

    private function applyKeywordFilter($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }
        return $query;
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
