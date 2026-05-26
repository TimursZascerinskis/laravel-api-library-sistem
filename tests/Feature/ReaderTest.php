<?php

namespace Tests\Feature;

use App\Models\Reader;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReaderTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_readers(): void
    {
        Reader::factory()->count(3)->create();

        $response = $this->getJson('/api/readers');

        $response->assertOk()->assertJsonCount(3);
    }

    public function test_store_creates_a_reader(): void
    {
        $data = [
            'vards' => 'Jānis Bērziņš',
            'e_pasts' => 'janis@example.com',
        ];

        $response = $this->postJson('/api/readers', $data);

        $response->assertCreated()
            ->assertJsonFragment($data);

        $this->assertDatabaseHas('readers', $data);
    }

    public function test_store_validates_required_fields(): void
    {
        $response = $this->postJson('/api/readers', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['vards', 'e_pasts']);
    }

    public function test_store_validates_email_format(): void
    {
        $response = $this->postJson('/api/readers', [
            'vards' => 'Jānis',
            'e_pasts' => 'nav-epasts',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['e_pasts']);
    }

    public function test_store_validates_unique_email(): void
    {
        Reader::factory()->create(['e_pasts' => 'janis@example.com']);

        $response = $this->postJson('/api/readers', [
            'vards' => 'Pēteris',
            'e_pasts' => 'janis@example.com',
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['e_pasts']);
    }

    public function test_show_returns_a_reader(): void
    {
        $reader = Reader::factory()->create();

        $response = $this->getJson("/api/readers/{$reader->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $reader->id]);
    }

    public function test_show_returns_404_for_missing_reader(): void
    {
        $response = $this->getJson('/api/readers/999');

        $response->assertNotFound();
    }

    public function test_update_modifies_a_reader(): void
    {
        $reader = Reader::factory()->create();

        $response = $this->putJson("/api/readers/{$reader->id}", [
            'vards' => 'Atjaunināts vārds',
        ]);

        $response->assertOk()
            ->assertJsonFragment(['vards' => 'Atjaunināts vārds']);

        $this->assertDatabaseHas('readers', [
            'id' => $reader->id,
            'vards' => 'Atjaunināts vārds',
        ]);
    }

    public function test_update_validates_unique_email_excluding_self(): void
    {
        $reader = Reader::factory()->create(['e_pasts' => 'janis@example.com']);
        $other = Reader::factory()->create();

        $response = $this->putJson("/api/readers/{$reader->id}", [
            'e_pasts' => 'janis@example.com',
        ]);

        $response->assertOk();

        $response = $this->putJson("/api/readers/{$reader->id}", [
            'e_pasts' => $other->e_pasts,
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['e_pasts']);
    }

    public function test_destroy_deletes_a_reader(): void
    {
        $reader = Reader::factory()->create();

        $response = $this->deleteJson("/api/readers/{$reader->id}");

        $response->assertNoContent();

        $this->assertDatabaseMissing('readers', ['id' => $reader->id]);
    }

    public function test_destroy_returns_404_for_missing_reader(): void
    {
        $response = $this->deleteJson('/api/readers/999');

        $response->assertNotFound();
    }
}
