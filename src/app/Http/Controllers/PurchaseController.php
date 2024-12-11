<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function show($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当の商品が存在しません。'], 404);
        }

        return view('purchase.show', compact('item'));
    }
}
