<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    // Toggle favorite (add/remove)
    public function toggle(Book $book)
    {
        $user = Auth::user();

        $user->favoriteBooks()->toggle($book->id);

        return back();
    }

    // List favorites
    public function index()
    {
        $favorites = Auth::user()->favoriteBooks;

        return view('favorites.index', compact('favorites'));
    }
}
