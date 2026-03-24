@extends('layouts.dashboard')

@section('content')

<h1 class="mb-6 text-2xl font-bold">Loans Management</h1>

<form method="GET" action="{{ route('loans.index') }}" class="mb-6 flex flex-wrap gap-3">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        placeholder="Search by user or book"
        class="rounded border-gray-300"
    >

    <select name="status" class="rounded border-gray-300">
        <option value="">All statuses</option>
        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
        <option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>Returned</option>
    </select>

    <select name="sort_by" class="rounded border-gray-300">
        <option value="borrowed_at" {{ request('sort_by', 'borrowed_at') === 'borrowed_at' ? 'selected' : '' }}>Borrowed Date</option>
        <option value="user" {{ request('sort_by') === 'user' ? 'selected' : '' }}>User Name</option>
        <option value="book" {{ request('sort_by') === 'book' ? 'selected' : '' }}>Book Title</option>
    </select>

    <select name="sort_direction" class="rounded border-gray-300">
        <option value="desc" {{ request('sort_direction', 'desc') === 'desc' ? 'selected' : '' }}>Newest First</option>
        <option value="asc" {{ request('sort_direction') === 'asc' ? 'selected' : '' }}>Oldest First</option>
    </select>

    <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-white">Apply</button>
</form>

@forelse($loans as $loan)
    <div class="mb-4 rounded bg-white p-4 shadow">
        <p><span class="font-medium">User:</span> {{ $loan->user->name }}</p>
        <p><span class="font-medium">Book:</span> {{ $loan->bookCopy->book->title }}</p>
        <p><span class="font-medium">Barcode:</span> {{ $loan->bookCopy->barcode }}</p>
        <p><span class="font-medium">Borrowed:</span> {{ $loan->borrowed_at->format('Y-m-d H:i') }}</p>
        <p><span class="font-medium">Returned:</span> {{ $loan->returned_at?->format('Y-m-d H:i') ?? 'Not returned' }}</p>
        <p><span class="font-medium">Status:</span> {{ $loan->returned_at ? 'Returned' : 'Active' }}</p>
        <p><span class="font-medium">Time Since Borrowed:</span> {{ $loan->returned_at ? '-' : $loan->borrowed_at->diffForHumans() }}</p>
    </div>
@empty
    <p>No loans matched the selected criteria.</p>
@endforelse

{{ $loans->links() }}

@endsection
