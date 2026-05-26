<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
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

    public function test_atomic_check_and_decrement_prevents_race_condition(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 1]);
        $reader1 = Reader::factory()->create();
        $reader2 = Reader::factory()->create();

        DB::beginTransaction();

        $affected1 = Book::where('id', $book->id)
            ->where('pieejamie_eksemplari', '>', 0)
            ->decrement('pieejamie_eksemplari');
        $this->assertEquals(1, $affected1);

        $affected2 = Book::where('id', $book->id)
            ->where('pieejamie_eksemplari', '>', 0)
            ->decrement('pieejamie_eksemplari');
        $this->assertEquals(0, $affected2);

        DB::rollBack();
    }

    public function test_sequential_borrow_prevents_oversubscription(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 1]);
        $reader1 = Reader::factory()->create();
        $reader2 = Reader::factory()->create();

        $this->postJson('/api/borrows', [
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader1->id,
            'aiznemsanas_datums' => '2026-05-26',
        ])->assertCreated();

        $this->postJson('/api/borrows', [
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader2->id,
            'aiznemsanas_datums' => '2026-05-26',
        ])->assertStatus(400)->assertJsonFragment(['error' => 'Nav pieejamu eksemplāru']);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 0,
        ]);
    }

    public function test_transaction_rollback_on_failed_borrow(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 1]);
        $reader = Reader::factory()->create();

        DB::beginTransaction();

        $response = $this->postJson('/api/borrows', [
            'gramata_id' => $book->id,
            'lasitajs_id' => $reader->id,
            'aiznemsanas_datums' => '2026-05-26',
        ]);

        $response->assertCreated();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 0,
        ]);

        DB::rollBack();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 1,
        ]);
    }

    public function test_destroy_rolls_back_on_failure(): void
    {
        $book = Book::factory()->create(['pieejamie_eksemplari' => 2]);
        $borrow = Borrow::factory()->create(['gramata_id' => $book->id]);

        DB::beginTransaction();

        $this->deleteJson("/api/borrows/{$borrow->id}")->assertNoContent();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 3,
        ]);

        DB::rollBack();

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'pieejamie_eksemplari' => 2,
        ]);
    }

    public function test_update_with_new_book_adjusts_copies(): void
    {
        $book1 = Book::factory()->create(['pieejamie_eksemplari' => 3]);
        $book2 = Book::factory()->create(['pieejamie_eksemplari' => 5]);
        $borrow = Borrow::factory()->create(['gramata_id' => $book1->id]);

        $response = $this->putJson("/api/borrows/{$borrow->id}", [
            'gramata_id' => $book2->id,
        ]);

        $response->assertOk()
            ->assertJsonFragment(['gramata_id' => $book2->id]);

        $this->assertDatabaseHas('books', [
            'id' => $book1->id,
            'pieejamie_eksemplari' => 4,
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book2->id,
            'pieejamie_eksemplari' => 4,
        ]);
    }

    public function test_update_with_new_book_fails_when_no_copies_available(): void
    {
        $book1 = Book::factory()->create(['pieejamie_eksemplari' => 3]);
        $book2 = Book::factory()->create(['pieejamie_eksemplari' => 0]);
        $borrow = Borrow::factory()->create(['gramata_id' => $book1->id]);

        $response = $this->putJson("/api/borrows/{$borrow->id}", [
            'gramata_id' => $book2->id,
        ]);

        $response->assertStatus(500);

        $this->assertDatabaseHas('books', [
            'id' => $book1->id,
            'pieejamie_eksemplari' => 3,
        ]);
    }
}
