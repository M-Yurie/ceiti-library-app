@extends('layouts.dashboard')

@section('content')

<h1 class="mb-6 text-2xl font-bold">My Borrowed Books</h1>

@forelse($loans as $loan)
    <div class="mb-4 rounded bg-white p-4 shadow">
        <p><span class="font-medium">Book:</span> {{ $loan->bookCopy->book->title }}</p>
        <p><span class="font-medium">Barcode:</span> {{ $loan->bookCopy->barcode }}</p>
        <p><span class="font-medium">Borrowed:</span> {{ $loan->borrowed_at->format('Y-m-d H:i') }}</p>
        <p><span class="font-medium">Time Since Borrowed:</span> {{ $loan->borrowed_at->diffForHumans() }}</p>
    </div>
@empty
    <p>You do not have any active loans.</p>
@endforelse

{{ $loans->links() }}

@endsection
