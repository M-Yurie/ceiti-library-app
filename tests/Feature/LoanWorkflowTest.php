<?php

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;

function librarianUser(): User
{
    $user = User::factory()->create();
    $user->forceFill(['role' => 'librarian'])->save();

    return $user;
}

test('librarian can find a copy by barcode and gets redirected to the borrow page', function () {
    $librarian = librarianUser();
    $book = Book::create([
        'title' => 'Patterns of Enterprise Application Architecture',
        'author' => 'Martin Fowler',
    ]);
    $copy = BookCopy::create([
        'book_id' => $book->id,
        'barcode' => 'BK123456',
        'status' => 'available',
    ]);

    $response = $this->actingAs($librarian)->post(route('loans.lookup'), [
        'barcode' => 'BK123456',
    ]);

    $response->assertRedirect(route('loans.create', ['copy' => $copy->id]));
});

test('barcode lookup returns an error when the copy is not found', function () {
    $librarian = librarianUser();

    $response = $this->from(route('loans.create'))
        ->actingAs($librarian)
        ->post(route('loans.lookup'), [
            'barcode' => 'MISSING-CODE',
        ]);

    $response->assertRedirect(route('loans.create'));
    $response->assertSessionHas('error', 'Book copy not found for the scanned barcode.');
});

test('librarian can search users by name', function () {
    $librarian = librarianUser();
    $student = User::factory()->create(['name' => 'Alice Johnson']);

    $response = $this->actingAs($librarian)
        ->get(route('loans.users.search', ['q' => 'Alice']));

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $student->id,
        'name' => 'Alice Johnson',
    ]);
});

test('librarian can borrow an available copy for a selected user', function () {
    $librarian = librarianUser();
    $student = User::factory()->create(['name' => 'Student Borrower']);
    $book = Book::create([
        'title' => 'Working Effectively with Legacy Code',
        'author' => 'Michael Feathers',
    ]);
    $copy = BookCopy::create([
        'book_id' => $book->id,
        'barcode' => 'BK654321',
        'status' => 'available',
    ]);

    $response = $this->actingAs($librarian)->post(route('loans.store', $copy), [
        'user_id' => $student->id,
    ]);

    $response->assertRedirect(route('loans.create', ['copy' => $copy->id]));
    $response->assertSessionHas('success', 'Book borrowed');

    $this->assertDatabaseHas('loans', [
        'book_copy_id' => $copy->id,
        'user_id' => $student->id,
        'returned_at' => null,
    ]);

    $this->assertDatabaseHas('book_copies', [
        'id' => $copy->id,
        'status' => 'borrowed',
    ]);
});

test('librarian can not borrow a copy that is already borrowed', function () {
    $librarian = librarianUser();
    $student = User::factory()->create(['name' => 'First Student']);
    $otherStudent = User::factory()->create(['name' => 'Second Student']);
    $book = Book::create([
        'title' => 'The Pragmatic Programmer',
        'author' => 'Andrew Hunt',
    ]);
    $copy = BookCopy::create([
        'book_id' => $book->id,
        'barcode' => 'BK999999',
        'status' => 'borrowed',
    ]);

    Loan::create([
        'book_copy_id' => $copy->id,
        'user_id' => $student->id,
        'borrowed_at' => now(),
        'due_date' => now()->addDays(14),
        'returned_at' => null,
    ]);

    $response = $this->from(route('loans.create', ['copy' => $copy->id]))
        ->actingAs($librarian)
        ->post(route('loans.store', $copy), [
            'user_id' => $otherStudent->id,
        ]);

    $response->assertRedirect(route('loans.create', ['copy' => $copy->id]));
    $response->assertSessionHas('error', 'Book not available');
    $this->assertDatabaseCount('loans', 1);
});
