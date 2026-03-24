<?php

use App\Models\Book;
use App\Models\BookCopy;
use App\Models\Loan;
use App\Models\User;

test('authenticated user sees only their active loans in my loans page', function () {
    $user = User::factory()->create(['name' => 'Loan Owner']);
    $otherUser = User::factory()->create(['name' => 'Other User']);

    $firstBook = Book::create([
        'title' => 'Code Complete',
        'author' => 'Steve McConnell',
    ]);
    $secondBook = Book::create([
        'title' => 'Design Patterns',
        'author' => 'GoF',
    ]);
    $thirdBook = Book::create([
        'title' => 'Algorithms',
        'author' => 'Sedgewick',
    ]);

    $firstCopy = BookCopy::create([
        'book_id' => $firstBook->id,
        'barcode' => 'MY-001',
        'status' => 'borrowed',
    ]);
    $secondCopy = BookCopy::create([
        'book_id' => $secondBook->id,
        'barcode' => 'MY-002',
        'status' => 'available',
    ]);
    $thirdCopy = BookCopy::create([
        'book_id' => $thirdBook->id,
        'barcode' => 'MY-003',
        'status' => 'borrowed',
    ]);

    Loan::create([
        'book_copy_id' => $firstCopy->id,
        'user_id' => $user->id,
        'borrowed_at' => now()->subDays(3),
        'due_date' => now()->addDays(11),
        'returned_at' => null,
    ]);

    Loan::create([
        'book_copy_id' => $secondCopy->id,
        'user_id' => $user->id,
        'borrowed_at' => now()->subDays(10),
        'due_date' => now()->subDays(1),
        'returned_at' => now()->subDay(),
    ]);

    Loan::create([
        'book_copy_id' => $thirdCopy->id,
        'user_id' => $otherUser->id,
        'borrowed_at' => now()->subDays(2),
        'due_date' => now()->addDays(12),
        'returned_at' => null,
    ]);

    $response = $this->actingAs($user)->get(route('loans.mine'));

    $response->assertOk();
    $response->assertSee('Code Complete');
    $response->assertSee('MY-001');
    $response->assertDontSee('Design Patterns');
    $response->assertDontSee('Algorithms');
});

test('librarian can search and filter loans on the management page', function () {
    $librarian = User::factory()->create(['name' => 'Librarian']);
    $librarian->forceFill(['role' => 'librarian'])->save();

    $matchingUser = User::factory()->create(['name' => 'Alice Reader']);
    $otherUser = User::factory()->create(['name' => 'Bob Reader']);

    $matchingBook = Book::create([
        'title' => 'Laravel in Action',
        'author' => 'Someone',
    ]);
    $otherBook = Book::create([
        'title' => 'Database Systems',
        'author' => 'Another Author',
    ]);

    $matchingCopy = BookCopy::create([
        'book_id' => $matchingBook->id,
        'barcode' => 'MG-001',
        'status' => 'borrowed',
    ]);
    $otherCopy = BookCopy::create([
        'book_id' => $otherBook->id,
        'barcode' => 'MG-002',
        'status' => 'available',
    ]);

    Loan::create([
        'book_copy_id' => $matchingCopy->id,
        'user_id' => $matchingUser->id,
        'borrowed_at' => now()->subDays(4),
        'due_date' => now()->addDays(10),
        'returned_at' => null,
    ]);

    Loan::create([
        'book_copy_id' => $otherCopy->id,
        'user_id' => $otherUser->id,
        'borrowed_at' => now()->subDays(7),
        'due_date' => now()->subDays(1),
        'returned_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($librarian)->get(route('loans.index', [
        'search' => 'Alice',
        'status' => 'active',
        'sort_by' => 'borrowed_at',
        'sort_direction' => 'desc',
    ]));

    $response->assertOk();
    $response->assertSee('Alice Reader');
    $response->assertSee('Laravel in Action');
    $response->assertSee('MG-001');
    $response->assertSee('Active');
    $response->assertDontSee('Bob Reader');
    $response->assertDontSee('Database Systems');
});

test('non staff users cannot access the loans management page', function () {
    $user = User::factory()->create();
    $user->forceFill(['role' => 'visitor'])->save();

    $this->actingAs($user)
        ->get(route('loans.index'))
        ->assertForbidden();
});
