<h1>{{ $book->title }} - Copies</h1>

<form method="POST" action="{{ route('copies.store', $book) }}">
    @csrf
    <button type="submit">Add Copy</button>
</form>

@foreach($copies as $copy)
    <div>
        <p>Barcode: {{ $copy->barcode }}</p>
        <p>Status: {{ $copy->status }}</p>

        <form method="POST" action="{{ route('copies.destroy', $copy) }}">
            @csrf
            @method('DELETE')
            <button>Delete</button>
        </form>
    </div>
@endforeach
