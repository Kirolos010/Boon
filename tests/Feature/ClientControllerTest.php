<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Client;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ClientControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
    }

    public function test_index_lists_clients()
    {
        Client::factory()->count(10)->create();

        $response = $this->actingAs($this->user)->get('/clients');

        $response->assertStatus(200);
        $response->assertViewIs('clients.index');
        $response->assertViewHas('clients');
    }

    public function test_create_shows_form()
    {
        $response = $this->actingAs($this->user)->get('/clients/create');

        $response->assertStatus(200);
        $response->assertViewIs('clients.create');
    }

    public function test_store_creates_client()
    {
        $data = [
            'name' => 'John Doe',
            'name_ar' => 'جون دو',
            'phone' => '0501234567',
            'email' => 'john@example.com',
            'address' => '123 Main Street',
            'credit_limit' => 5000,
        ];

        $response = $this->actingAs($this->user)->post('/clients', $data);

        $response->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', [
            'name' => 'John Doe',
            'phone' => '0501234567'
        ]);
    }

    public function test_store_fails_with_duplicate_email()
    {
        Client::factory()->create(['email' => 'john@example.com']);

        $data = [
            'name' => 'Jane Doe',
            'name_ar' => 'جين دو',
            'phone' => '0509876543',
            'email' => 'john@example.com',
        ];

        $response = $this->actingAs($this->user)->post('/clients', $data);

        $response->assertSessionHasErrors('email');
    }

    public function test_show_returns_client_json()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->get("/clients/{$client->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_edit_shows_form()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->get("/clients/{$client->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('clients.edit');
    }

    public function test_update_modifies_client()
    {
        $client = Client::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'name_ar' => 'اسم محدث',
            'phone' => '0555555555',
            'email' => $client->email,
            'credit_limit' => 10000,
        ];

        $response = $this->actingAs($this->user)->patch("/clients/{$client->id}", $data);

        $response->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Updated Name'
        ]);
    }

    public function test_destroy_soft_deletes_client()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->delete("/clients/{$client->id}");

        $response->assertJson(['status' => 'success']);
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_invoices_returns_client_invoices()
    {
        $client = Client::factory()->create();
        Invoice::factory()->count(5)->create(['client_id' => $client->id]);

        $response = $this->actingAs($this->user)->get("/clients/{$client->id}/invoices");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_client_requires_authentication()
    {
        $response = $this->get('/clients');

        $response->assertRedirect('/login');
    }

    public function test_store_requires_required_fields()
    {
        $data = [
            'name' => 'John Doe',
            'name_ar' => 'جون دو',
            // Missing phone
        ];

        $response = $this->actingAs($this->user)->post('/clients', $data);

        $response->assertSessionHasErrors('phone');
    }

    public function test_client_gets_available_credit()
    {
        $client = Client::factory()->create(['credit_limit' => 5000, 'total_debt' => 2000]);

        $available = $client->getAvailableCredit();

        $this->assertEquals(3000, $available);
    }

    public function test_client_validates_credit_availability()
    {
        $client = Client::factory()->create(['credit_limit' => 5000, 'total_debt' => 2000]);

        $this->assertTrue($client->hasAvailableCredit(2000));
        $this->assertFalse($client->hasAvailableCredit(4000));
    }
}
