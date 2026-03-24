<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookCopy;
use App\Models\Loan;

class LoanController extends Controller
{
    public function store(Request $request, BookCopy $copy)
    {
        if ($copy->status !== 'available') {
            return back()->with('error', 'Book not available');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'due_date' => 'required|date'
        ]);

        Loan::create([
            'book_copy_id' => $copy->id,
            'user_id' => $request->user_id,
            'borrowed_at' => now(),
            'due_date' => $request->due_date,
        ]);

        $copy->update([
            'status' => 'borrowed'
        ]);

        return back()->with('success', 'Book borrowed');
    }

    public function returnBook(BookCopy $copy)
    {
        $loan = $copy->loan;

        if (!$loan) {
            return back()->with('error', 'No active loan');
        }

        $loan->update([
            'returned_at' => now()
        ]);

        $copy->update([
            'status' => 'available'
        ]);

        return back()->with('success', 'Book returned');
    }
}
