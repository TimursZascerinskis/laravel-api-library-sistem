<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_books(): void
    {
        Book::factory()->count(3)->create();

        $response = $this->getJson('/api/books');

        $response->assertOk()->assertJsonCount(3);
    }

    public function test_store_creates_a_book(): void
    {
        $data = [
            'nosaukums' => 'Testa grāmata',
            'isbn' => '978-3-16-148410-0',
            'pieejamie_eksemplari' => 5,
        ];

        $response = $this->postJson('/api/books', $data);

        $response->assertCreated()
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('books', $data);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/books', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['nosaukums', 'isbn', 'pieejamie_eksemplari']);
    }

    public function test_store_validates_unique_isbn(): void
    {
        Book::factory()->create(['isbn' => '978-3-16-148410-0']);

        $response = $this->postJson('/api/books', [
            'nosaukums' => 'Vēl viena grāmata',
            'isbn' => '978-3-16-148410-0',
            'pieejamie_eksemplari' => 1,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['isbn']);
    }

    public function test_show_returns_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/books/{$book->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $book->id]);
    }

    public function test_show_returns_404_for_missing_book(): void
    {
        $response = $this->getJson('/api/books/999');

        $response->assertNotFound();
    }

    public function test_update_modifies_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->putJson("/api/books/{$book->id}", [
            'nosaukums' => 'Atjaunināts nosaukums',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['nosaukums' => 'Atjaunināts nosaukums']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'nosaukums' => 'Atjaunināts nosaukums',
        ]);
    }

    public function test_update_validates_unique_isbn_excluding_self(): void
    {
        $book = Book::factory()->create(['isbn' => '978-3-16-148410-0']);
        $other = Book::factory()->create();

        $response = $this->putJson("/api/books/{$book->id}", [
            'isbn' => '978-3-16-148410-0',
        ]);

        $response->assertOk();

        $response = $this->putJson("/api/books/{$book->id}", [
            'isbn' => $other->isbn,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['isbn']);
    }

    public function test_destroy_deletes_a_book(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/books/{$book->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
    }

    public function test_destroy_returns_404_for_missing_book(): void
    {
        $response = $this->deleteJson('/api/books/999');

        $response->assertNotFound();
    }
}
