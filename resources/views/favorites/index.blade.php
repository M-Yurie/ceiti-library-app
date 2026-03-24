@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-bold mb-6">My Favorites</h1>

@if($favorites->isEmpty())
    <p>No favorite books yet.</p>
@else
    <div>
        @foreach($favorites as $book)
            <div class="mb-3">
                <h3 class="font-bold">{{ $book->title }}</h3>
                <p>{{ $book->author }}</p>
            </div>
        @endforeach
    </div>
@endif

@endsection
