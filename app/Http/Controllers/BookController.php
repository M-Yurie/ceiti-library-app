<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

// protected $fillable = [
//         'title',
//         'author',
//         'description',
//         'publisher',
//         'category_id',
//         'language',
//         'cover_image',
//     ];

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Book::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('author', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if (in_array($request->sort, ['asc', 'desc'], true)) {
            $query->orderBy('title', $request->sort);
        } else {
            $query->latest();
        }

        $books = $query->paginate(10)->withQueryString();

        if (auth()->check()) {
            auth()->user()->loadMissing('favoriteBooks');
        }

        return view('books.index', compact('books'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required',
            'author' => 'required',
            // 'description' => 'required',
            // 'publisher' => 'required',
            // 'category_id' => 'required|exists:categories,id',
            // 'language' => 'required',
            // 'cover_image' => 'nullable|image|max:2048',
        ]);

        Book::create($validated);

        return redirect()->route('books.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        $book->load([
            'category',
            'ratings.user',
            'comments' => fn ($query) => $query->with('user')->latest(),
        ]);

        $averageRating = round((float) $book->ratings->avg('rating'), 1);
        $userRating = auth()->user()?->ratings()
            ->where('book_id', $book->id)
            ->value('rating');

        return view('books.show', compact('book', 'averageRating', 'userRating'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        return view('books.edit', compact('book'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'required',
            'author' => 'required',
            // 'description' => 'required',
            // 'publisher' => 'required',
            // 'category_id' => 'required|exists:categories,id',
            // 'language' => 'required',
            // 'cover_image' => 'nullable|image|max:2048',
        ]);

        $book->update($validated);

        return redirect()->route('books.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $book->delete();

        return redirect()->route('books.index');
    }
}
