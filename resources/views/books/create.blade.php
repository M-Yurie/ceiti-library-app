<h1>Add Book</h1>

<form method="POST" action="{{ route('books.store') }}">
    @csrf

    <input name="title" placeholder="Title">
    <input name="author" placeholder="Author">
    {{-- <textarea name="description" placeholder="Description"></textarea>
    <input name="publisher" placeholder="Publisher">
    <input name="category_id" placeholder="Category ID">
    <input name="language" placeholder="Language">
    <input name="cover_image" placeholder="Cover Image URL"> --}}

    <button type="submit">Save</button>
</form>
