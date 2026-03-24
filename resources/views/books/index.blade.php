@extends('layouts.dashboard')

@section('content')

@php($user = auth()->user())

<h1 class="mb-6 text-2xl font-bold">Books</h1>

@if($user->isAdmin() || $user->isLibrarian())
    <a href="{{ route('books.create') }}" class="mb-4 inline-block text-blue-600">Add Book</a>
@endif

<form method="GET" action="{{ route('books.index') }}" class="mb-6 flex flex-wrap gap-3">
    <input
        type="text"
        name="search"
        placeholder="Search by title or author"
        value="{{ request('search') }}"
        class="rounded border-gray-300"
    >

    <select name="category_id" class="rounded border-gray-300">
        <option value="">All categories</option>
        @foreach(\App\Models\Category::all() as $category)
            <option value="{{ $category->id }}"
                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>

    <select name="sort" class="rounded border-gray-300">
        <option value="">Newest</option>
        <option value="asc" {{ request('sort') === 'asc' ? 'selected' : '' }}>A-Z</option>
        <option value="desc" {{ request('sort') === 'desc' ? 'selected' : '' }}>Z-A</option>
    </select>

    <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-white">Filter</button>
</form>

@forelse($books as $book)
    @php($isFavorite = $user->favoriteBooks->contains($book->id))

    <div class="mb-4 rounded bg-white p-4 shadow">
        <h3 class="text-lg font-semibold">{{ $book->title }}</h3>
        <p class="mb-3 text-gray-700">{{ $book->author }}</p>

        <div class="flex flex-wrap gap-3">
            <form action="{{ route('favorites.toggle', $book) }}" method="POST">
                @csrf
                <button type="submit" class="text-red-600">
                    {{ $isFavorite ? 'Remove Favorite' : 'Add Favorite' }}
                </button>
            </form>

            @if($user->isAdmin() || $user->isLibrarian())
                <a href="{{ route('books.edit', $book) }}" class="text-blue-600">Edit</a>
                <a href="{{ route('copies.index', $book) }}" class="text-blue-600">View Copies</a>

                <form method="POST" action="{{ route('books.destroy', $book) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600">Delete</button>
                </form>
            @endif
        </div>
    </div>
@empty
    <p>No books found.</p>
@endforelse

{{ $books->links() }}

@endsection
