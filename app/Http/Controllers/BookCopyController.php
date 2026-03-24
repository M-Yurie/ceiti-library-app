<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\BookCopy;

class BookCopyController extends Controller
{
    public function index(Book $book)
    {
        $copies = $book->copies;
        return view('copies.index', compact('book', 'copies'));
    }

    public function store(Book $book)
    {
        $barcode = 'BK' . strtoupper(uniqid());

        $book->copies()->create([
            'barcode' => $barcode,
            'status' => 'available'
        ]);

        return back();
    }

    public function destroy(BookCopy $copy)
    {
        $copy->delete();
        return back();
    }
}
