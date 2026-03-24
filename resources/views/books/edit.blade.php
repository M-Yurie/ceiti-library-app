<h1>Edit Book</h1>

<form method="POST" action="{{ route('books.update', $book) }}">
    @csrf
    @method('PUT')

    <input name="title" value="{{ $book->title }}">
    <input name="author" value="{{ $book->author }}">
    {{-- <input name="description" value="{{ $book->description }}">
    <input name="publisher" value="{{ $book->publisher }}">
    <input name="category_id" value="{{ $book->category_id }}">
    <input name="language" value="{{ $book->language }}">
    <input name="cover_image" value="{{ $book->cover_image }}"> --}}

    <button type="submit">Update</button>
</form>

