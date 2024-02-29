<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function index()
    {
        if(auth()->user()){
            $feedbackList = Feedback::with('user')->paginate(10);
            return response()->json($feedbackList);
        } else {
            return response()->json(['message' => 'user not authenticated'], 401);
        }
        
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:255',
        ]);

        $feedback = new Feedback();
        $feedback->title = $request->title;
        $feedback->description = $request->description;
        $feedback->category = $request->category;
        $feedback->user_id = auth()->id();
        $feedback->save();

        return response()->json(['message' => 'Feedback submitted successfully']);
    }
}
