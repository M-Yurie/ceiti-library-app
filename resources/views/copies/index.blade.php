<h1>{{ $book->title }} - Copies</h1>

<form method="POST" action="{{ route('copies.store', $book) }}">
    @csrf
    <button type="submit">Add Copy</button>
</form>

@foreach($copies as $copy)
    <div>
        <p>Barcode: {{ $copy->barcode }}</p>
        <p>Status: {{ $copy->status }}</p>

        <form method="POST" action="{{ route('loans.store', $copy) }}">
            @csrf

            <input name="user_id" placeholder="User ID">
            <input type="date" name="due_date">

            <button type="submit">Borrow</button>
        </form>

        <form method="POST" action="{{ route('loans.return', $copy) }}">
            @csrf

            <button type="submit">Return</button>
        </form>
    </div>
@endforeach
