<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BorrowTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_borrows_with_relations(): void
    {
        Borrow::factory()->count(3)->create();

        $response = $this->getJson('/api/borrows');

        $response->assertOk()->assertJsonCount(3);

        $response->assertJsonStructure([
            '*' => ['id', 'gramata_id', 'lasitajs_id', 'aiznemsanas_datums', 'atdosanas_datums', 'book', 'reader'],
        ]);
    }

    public function test_store_creates_a_borrow_and_decrements_copies(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 5]);
        $reader = Reader::factory()->create();

        $data = [
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader->id,
            'aiznemsanas_datums' => '2026-05-26',
            'atdosanas_datums' => '2026-06-26',
        ];

        $response = $this->postJson('/api/borrows', $data);

        $response->assertCreated()
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('borrows', $data);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 4,
        ]);
    }

    public function test_store_fails_when_no_available_copies(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 0]);
        $reader = Reader::factory()->create();

        $response = $this->postJson('/api/borrows', [
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader->id,
            'aiznemsanas_datums' => '2026-05-26',
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'Nav pieejamu eksemplāru']);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/borrows', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['gramata_id', 'lasitajs_id', 'aiznemsanas_datums']);
    }

    public function test_store_validates_foreign_keys_exist(): void
    {
        $response = $this->postJson('/api/borrows', [
            'gramata_id' => 999,
            'lasitajs_id' => 999,
            'aiznemsanas_datums' => '2026-05-26',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['gramata_id', 'lasitajs_id']);
    }

    public function test_show_returns_a_borrow_with_relations(): void
    {
        $borrow = Borrow::factory()->create();

        $response = $this->getJson("/api/borrows/{$borrow->id}");

        $response->assertOk()
            ->assertJsonStructure(['id', 'gramata_id', 'lasitajs_id', 'book', 'reader']);
    }

    public function test_show_returns_404_for_missing_borrow(): void
    {
        $response = $this->getJson('/api/borrows/999');

        $response->assertNotFound();
    }

    public function test_update_modifies_a_borrow(): void
    {
        $borrow = Borrow::factory()->create();
        $newBook = Book::factory()->create();

        $response = $this->putJson("/api/borrows/{$borrow->id}", [
            'gramata_id' => $newBook->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['gramata_id' => $newBook->id]);
    }

    public function test_destroy_deletes_a_borrow_and_increments_copies(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 2]);
        $borrow = Borrow::factory()->create(['gramata_id' => $book->id]);

        $response = $this->deleteJson("/api/borrows/{$borrow->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('borrows', ['id' => $borrow->id]);
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 3,
        ]);
    }

    public function test_destroy_returns_404_for_missing_borrow(): void
    {
        $response = $this->deleteJson('/api/borrows/999');

        $response->assertNotFound();
    }
}
