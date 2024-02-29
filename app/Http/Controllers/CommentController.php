<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index($feedbackId)
    {
        $comments = Comment::where('feedback_id', $feedbackId)->with('user')->paginate(10);
        return response()->json($comments);
    }

    public function store(Request $request, $feedbackId)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $request->content;
        $comment->user_id = auth()->id();
        $comment->feedback_id = $feedbackId;
        $comment->save();

        return response()->json(['message' => 'Comment added successfully']);
    }
}
