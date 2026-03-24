<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function storeRating(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
        ]);

        Rating::updateOrCreate(
            [
                'user_id' => $request->user()->id,
                'book_id' => $book->id,
            ],
            [
                'rating' => $validated['rating'],
            ]
        );

        return back();
    }

    public function storeComment(Request $request, Book $book): RedirectResponse
    {
        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        Comment::create([
            'user_id' => $request->user()->id,
            'book_id' => $book->id,
            'content' => $validated['content'],
        ]);

        return back();
    }
}
