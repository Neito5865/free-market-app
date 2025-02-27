<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function store(Request $request, $id)
    {
        \Auth::user()->favorite($id);
        return redirect()->back();
    }

    public function destroy(Request $request, $id){
        \Auth::user()->unfavorite($id);
        return redirect()->back();
    }
}
