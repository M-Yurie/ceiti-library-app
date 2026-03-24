<?php

namespace App\Http\Controllers;

use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanController extends Controller
{
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
