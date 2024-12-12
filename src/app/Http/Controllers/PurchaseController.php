<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class PurchaseController extends Controller
{
    public function show($id)
    {
        $user = Auth::user();
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当の商品が存在しません。'], 404);
        }
        $selectedAddress = session('selected_address', null);

        return view('purchase.show', compact('item', 'user', 'selectedAddress'));
    }
}
