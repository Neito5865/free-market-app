<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;
use App\Models\Item;
use App\Http\Requests\CommentRequest;


class CommentsController extends Controller
{
    public function store(CommentRequest $request, $id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->view('errors.error-page', ['message' => 'ページを表示できません。'], 404);
        }
        $user_id = Auth::id();
        $commentData = $request->only([
            'comment',
        ]);
        $commentData['user_id'] = $user_id;
        $commentData['item_id'] = $item->id;
        $comment = Comment::create($commentData);

        return back();
    }
}
