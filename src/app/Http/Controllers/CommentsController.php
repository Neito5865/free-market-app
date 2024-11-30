<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment;

class CommentsController extends Controller
{
    public function store(Request $request)
    {
        $user_id = Auth::id();
        $commentData = $request->only([
            'comment',
        ]);
        $commentData['user_id'] = $user_id;
        $comment = Comment::create($commentData);

        return back()->with('success', 'コメントが登録されました');
    }
}
