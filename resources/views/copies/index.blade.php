@extends('layouts.dashboard')

@section('content')

<h1 class="mb-6 text-2xl font-bold">{{ $book->title }} Copies</h1>

<div class="mb-6 flex flex-wrap gap-3">
    <form method="POST" action="{{ route('copies.store', $book) }}">
        @csrf
        <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-white">Add Copy</button>
    </form>

    <a href="{{ route('loans.create') }}" class="rounded border px-4 py-2 text-gray-700">Borrow by Barcode</a>
</div>

@forelse($copies as $copy)
    <div class="mb-4 rounded bg-white p-4 shadow">
        <p><span class="font-medium">Barcode:</span> {{ $copy->barcode }}</p>
        <p><span class="font-medium">Status:</span> {{ ucfirst($copy->status) }}</p>

        @if($copy->loan)
            <p><span class="font-medium">Borrowed By:</span> {{ $copy->loan->user->name }}</p>
        @endif

        <div class="mt-3 flex flex-wrap gap-3">
            @if($copy->status === 'available')
                <a href="{{ route('loans.create', ['copy' => $copy->id]) }}" class="text-blue-600">Borrow This Copy</a>
            @else
                <form method="POST" action="{{ route('loans.return', $copy) }}">
                    @csrf
                    <button type="submit" class="text-blue-600">Return Copy</button>
                </form>
            @endif

            <form method="POST" action="{{ route('copies.destroy', $copy) }}">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">Delete</button>
            </form>
        </div>
    </div>
@empty
    <p>No copies available for this book yet.</p>
@endforelse

@endsection
