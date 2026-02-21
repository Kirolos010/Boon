<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $client;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
        $this->client = Client::factory()->create();
        $this->product = Product::factory()->create(['current_stock_kg' => 100]);
    }

    public function test_index_lists_invoices()
    {
        Invoice::factory()->count(5)->create(['type' => 'regular', 'client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->get('/invoices');

        $response->assertStatus(200);
        $response->assertViewIs('invoices.index');
        $response->assertViewHas('invoices');
    }

    public function test_create_shows_form()
    {
        $response = $this->actingAs($this->user)->get('/invoices/create');

        $response->assertStatus(200);
        $response->assertViewIs('invoices.create');
    }

    public function test_store_creates_invoice()
    {
        $data = [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_kg' => 10,
                    'unit_price' => 150
                ]
            ],
            'discount' => 0,
            'tax' => 0,
            'amount_paid' => 0,
            'type' => 'regular'
        ];

        $response = $this->actingAs($this->user)->post('/invoices', $data);

        $response->assertRedirect('/invoices');
        $this->assertDatabaseHas('invoices', ['type' => 'regular']);
    }

    public function test_store_fails_without_items()
    {
        $data = [
            'client_id' => $this->client->id,
            'items' => [],
            'discount' => 0,
            'tax' => 0,
        ];

        $response = $this->actingAs($this->user)->post('/invoices', $data);

        $response->assertSessionHasErrors('items');
    }

    public function test_show_displays_invoice()
    {
        $invoice = Invoice::factory()->has(
            InvoiceItem::factory()->count(1),
            'items'
        )->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}");

        $response->assertStatus(200);
        $response->assertViewIs('invoices.show');
        $response->assertViewHas('invoice');
    }

    public function test_edit_shows_form()
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->get("/invoices/{$invoice->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('invoices.edit');
    }

    public function test_destroy_cancels_invoice()
    {
        $invoice = Invoice::factory()->create(['client_id' => $this->client->id]);

        $response = $this->actingAs($this->user)->delete("/invoices/{$invoice->id}");

        $response->assertRedirect('/invoices');
        $this->assertSoftDeleted('invoices', ['id' => $invoice->id]);
    }

    public function test_record_payment_adds_payment()
    {
        $invoice = Invoice::factory()->create([
            'client_id' => $this->client->id,
            'total' => 1500,
            'amount_paid' => 0,
            'status' => 'unpaid'
        ]);

        $response = $this->actingAs($this->user)->post("/invoices/{$invoice->id}/record-payment", [
            'amount' => 500,
            'payment_method' => 'cash',
            'payment_date' => now()->toDateString(),
            'notes' => 'Test payment'
        ]);

        $response->assertRedirect("/invoices/{$invoice->id}");
        $this->assertDatabaseHas('invoice_payments', [
            'invoice_id' => $invoice->id,
            'amount' => 500
        ]);
    }

    public function test_invoice_requires_authentication()
    {
        $response = $this->get('/invoices');

        $response->assertRedirect('/login');
    }

    public function test_invoice_stock_is_deducted_on_create()
    {
        $initial_stock = $this->product->current_stock_kg;

        $data = [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_kg' => 10,
                    'unit_price' => 150
                ]
            ],
            'discount' => 0,
            'tax' => 0,
        ];

        $this->actingAs($this->user)->post('/invoices', $data);

        $this->product->refresh();
        $this->assertEquals($initial_stock - 10, $this->product->current_stock_kg);
    }

    public function test_invoice_fails_with_insufficient_stock()
    {
        $data = [
            'client_id' => $this->client->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity_kg' => 150,  // More than available
                    'unit_price' => 150
                ]
            ],
            'discount' => 0,
            'tax' => 0,
        ];

        $response = $this->actingAs($this->user)->post('/invoices', $data);

        $response->assertSessionHasErrors();
    }
}
