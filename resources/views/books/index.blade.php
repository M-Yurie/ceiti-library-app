<h1>Books</h1>

<a href="{{ route('books.create') }}">Add Book</a>

@foreach($books as $book)
    <div>
        <h3>{{ $book->title }}</h3>
        <p>{{ $book->author }}</p>

        <a href="{{ route('books.edit', $book) }}">Edit</a>
        <a href="{{ route('copies.index', $book) }}">View Copies</a>

        <form method="POST" action="{{ route('books.destroy', $book) }}">
            @csrf
            @method('DELETE')
            <button type="submit">Delete</button>
        </form>
    </div>
@endforeach
