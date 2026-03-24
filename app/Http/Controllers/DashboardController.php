<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use Illuminate\Http\RedirectResponse;

class DashboardController extends Controller
{
    public function index(): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isLibrarian()) {
            return redirect()->route('librarian.dashboard');
        }

        return redirect()->route('books.index');
    }

    public function admin()
    {
        return view('admin.dashboard', [
            'totalBooks' => Book::count(),
            'totalCopies' => BookCopy::count(),
            'totalLoans' => Loan::count(),
            'activeLoans' => Loan::whereNull('returned_at')->count(),
        ]);
    }

    public function librarian()
    {
        return view('librarian.dashboard', [
            'totalBooks' => Book::count(),
            'totalCopies' => BookCopy::count(),
            'activeLoans' => Loan::whereNull('returned_at')->count(),
        ]);
    }
}
