<?php

use App\Models\Book;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\User;

test('authenticated users can view a book detail page with reviews', function () {
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Domain-Driven Design',
        'author' => 'Eric Evans',
        'category_id' => Category::create(['name' => 'Software'])->id,
    ]);

    Rating::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'rating' => 4,
    ]);

    Comment::create([
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'Useful reference.',
    ]);

    $response = $this->actingAs($user)->get(route('books.show', $book));

    $response->assertOk();
    $response->assertSee('Domain-Driven Design');
    $response->assertSee('4.0 / 5');
    $response->assertSee('Useful reference.');
    $response->assertSee($user->name);
});

test('users can rate a book and update their existing rating', function () {
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Clean Code',
        'author' => 'Robert C. Martin',
    ]);

    $this->actingAs($user)->post(route('books.rating.store', $book), [
        'rating' => 5,
    ])->assertRedirect();

    $this->actingAs($user)->post(route('books.rating.store', $book), [
        'rating' => 3,
    ])->assertRedirect();

    $this->assertDatabaseCount('ratings', 1);
    $this->assertDatabaseHas('ratings', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'rating' => 3,
    ]);
});

test('users can comment on a book', function () {
    $user = User::factory()->create();
    $book = Book::create([
        'title' => 'Refactoring',
        'author' => 'Martin Fowler',
    ]);

    $this->actingAs($user)->post(route('books.comment.store', $book), [
        'content' => 'This book should be on the must-read list.',
    ])->assertRedirect();

    $this->assertDatabaseHas('comments', [
        'user_id' => $user->id,
        'book_id' => $book->id,
        'content' => 'This book should be on the must-read list.',
    ]);
});
