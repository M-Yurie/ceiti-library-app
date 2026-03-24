@extends('layouts.dashboard')

@section('content')

<div class="rounded bg-white p-6 shadow">
    <h1 class="mb-2 text-2xl font-bold">{{ $book->title }}</h1>
    <p class="mb-1 text-gray-700">Author: {{ $book->author }}</p>

    @if($book->category)
        <p class="mb-1 text-gray-700">Category: {{ $book->category->name }}</p>
    @endif

    @if($book->description)
        <p class="mb-4 text-gray-700">{{ $book->description }}</p>
    @endif

    <p class="mb-4 font-semibold">
        Average rating:
        {{ $book->ratings->isNotEmpty() ? number_format($averageRating, 1) . ' / 5' : 'No ratings yet' }}
    </p>

    <form method="POST" action="{{ route('books.rating.store', $book) }}" class="mb-6">
        @csrf

        <label for="rating" class="mb-2 block font-medium">Your rating</label>
        <select id="rating" name="rating" class="rounded border-gray-300">
            @for($rating = 1; $rating <= 5; $rating++)
                <option value="{{ $rating }}" {{ (int) old('rating', $userRating) === $rating ? 'selected' : '' }}>
                    {{ $rating }}
                </option>
            @endfor
        </select>

        @error('rating')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit" class="ml-3 rounded bg-gray-800 px-4 py-2 text-white">Save Rating</button>
    </form>

    <form method="POST" action="{{ route('books.comment.store', $book) }}" class="mb-6">
        @csrf

        <label for="content" class="mb-2 block font-medium">Comment</label>
        <textarea
            id="content"
            name="content"
            rows="4"
            class="w-full rounded border-gray-300"
            placeholder="Write your comment"
        >{{ old('content') }}</textarea>

        @error('content')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <button type="submit" class="mt-3 rounded bg-gray-800 px-4 py-2 text-white">Post Comment</button>
    </form>

    <h2 class="mb-3 text-xl font-semibold">Comments</h2>

    @forelse($book->comments as $comment)
        <div class="mb-3 rounded border p-3">
            <p class="font-medium">{{ $comment->user->name }}</p>
            <p class="text-gray-700">{{ $comment->content }}</p>
        </div>
    @empty
        <p class="text-gray-700">No comments yet.</p>
    @endforelse
</div>

@endsection
