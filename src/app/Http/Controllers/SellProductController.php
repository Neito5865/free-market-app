<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SellProductController extends Controller
{
    public function create()
    {
        return view('items.create');
    }
}
