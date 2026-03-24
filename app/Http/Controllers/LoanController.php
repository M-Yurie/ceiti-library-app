<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function myLoans(Request $request): View
    {
        $loans = Loan::query()
            ->with(['bookCopy.book'])
            ->where('user_id', $request->user()->id)
            ->whereNull('returned_at')
            ->latest('borrowed_at')
            ->paginate(10);

        return view('loans.my-loans', compact('loans'));
    }

    public function index(Request $request): View
    {
        $query = Loan::query()->with(['user', 'bookCopy.book']);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($builder) use ($search) {
                $builder->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('bookCopy.book', function ($bookQuery) use ($search) {
                    $bookQuery->where('title', 'like', '%' . $search . '%');
                });
            });
        }

        if ($request->status === 'active') {
            $query->whereNull('returned_at');
        } elseif ($request->status === 'returned') {
            $query->whereNotNull('returned_at');
        }

        $direction = $request->sort_direction === 'asc' ? 'asc' : 'desc';

        switch ($request->sort_by) {
            case 'user':
                $query->orderBy(
                    User::select('name')
                        ->whereColumn('users.id', 'loans.user_id')
                        ->limit(1),
                    $direction
                );
                break;
            case 'book':
                $query->orderBy(
                    BookCopy::select('books.title')
                        ->join('books', 'books.id', '=', 'book_copies.book_id')
                        ->whereColumn('book_copies.id', 'loans.book_copy_id')
                        ->limit(1),
                    $direction
                );
                break;
            default:
                $query->orderBy('borrowed_at', $direction);
                break;
        }

        $loans = $query->paginate(15)->withQueryString();

        return view('loans.index', compact('loans'));
    }

    public function create(Request $request): View
    {
        $selectedCopy = null;

        if ($request->filled('copy')) {
            $selectedCopy = BookCopy::with(['book', 'loan.user'])->find($request->copy);
        }

        return view('loans.create', compact('selectedCopy'));
    }

    public function lookupByBarcode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'barcode' => ['required', 'string'],
        ]);

        $copy = BookCopy::where('barcode', $validated['barcode'])->first();

        if (! $copy) {
            return back()
                ->withInput()
                ->with('error', 'Book copy not found for the scanned barcode.');
        }

        return redirect()->route('loans.create', ['copy' => $copy->id]);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $term = trim((string) $request->query('q', ''));

        if ($term === '') {
            return response()->json([]);
        }

        $users = User::query()
            ->where('name', 'like', '%' . $term . '%')
            ->orderBy('name')
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($users);
    }

    public function store(Request $request, BookCopy $copy)
    {
        if ($copy->status !== 'available' || $copy->loan()->exists()) {
            return back()->with('error', 'Book not available');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
        ]);

        Loan::create([
            'book_copy_id' => $copy->id,
            'user_id' => $validated['user_id'],
            'borrowed_at' => now(),
            'due_date' => $validated['due_date'] ?? now()->addDays(14),
            'returned_at' => null,
        ]);

        $copy->update([
            'status' => 'borrowed',
        ]);

        return redirect()
            ->route('loans.create', ['copy' => $copy->id])
            ->with('success', 'Book borrowed');
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
