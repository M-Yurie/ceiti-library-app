<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\User;

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

        $query->where(function ($q) use ($request) {
            $q->where('title', 'like', '%' . $request->search . '%')
            ->orWhere('author', 'like', '%' . $request->search . '%');
        });

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('sort')) {
            $query->orderBy('title', $request->sort);
        }

        $books = $query->latest()->paginate(10);

        if (auth()->check()) {
            auth()->user()->load('favoriteBooks');
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
    public function show(string $id)
    {
        //
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
