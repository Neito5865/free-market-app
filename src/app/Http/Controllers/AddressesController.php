<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Item;
use App\Http\Requests\AddressRequest;

class AddressesController extends Controller
{
    public function create($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当のページが存在しません。'], 404);
        }
        return view('addresses.create', compact('item'));
    }

    public function store(AddressRequest $request, $id)
    {
        Session::put('selected_address', $request->only([
            'name', 'post_code', 'address', 'building'
        ]));
        return redirect()->route('purchase.create', ['id' => $id]);
    }
}
