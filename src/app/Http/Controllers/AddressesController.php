<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class AddressesController extends Controller
{
    public function create($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => '該当のページが存在しません。'], 404);
        }
        return view('addresses.create');
    }
}
