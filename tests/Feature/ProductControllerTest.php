<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\MainCategory;
use App\Models\SubCategory;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $mainCategory;
    protected $subCategory;
    protected $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role_id' => 1  // Admin
        ]);
        $this->mainCategory = MainCategory::factory()->create();
        $this->subCategory = SubCategory::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    public function test_index_lists_all_products()
    {
        Product::factory()->count(10)->create();

        $response = $this->actingAs($this->user)->get('/products');

        $response->assertStatus(200);
        $response->assertViewIs('products.index');
        $response->assertViewHas('products');
    }

    public function test_create_shows_form()
    {
        $response = $this->actingAs($this->user)->get('/products/create');

        $response->assertStatus(200);
        $response->assertViewIs('products.create');
    }

    public function test_store_creates_product()
    {
        $data = [
            'name' => 'Test Product',
            'name_ar' => 'منتج اختبار',
            'sku' => 'TEST-001',
            'main_category_id' => $this->mainCategory->id,
            'sub_category_id' => $this->subCategory->id,
            'supplier_id' => $this->supplier->id,
            'purchase_price_per_kg' => 100,
            'selling_price_per_kg' => 150,
            'minimum_stock_alert' => 10,
            'current_stock_kg' => 50,
        ];

        $response = $this->actingAs($this->user)->post('/products', $data);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'sku' => 'TEST-001',
            'name' => 'Test Product'
        ]);
    }

    public function test_store_returns_error_for_duplicate_sku()
    {
        Product::factory()->create(['sku' => 'TEST-001']);

        $data = [
            'name' => 'Another Product',
            'name_ar' => 'منتج آخر',
            'sku' => 'TEST-001',
            'main_category_id' => $this->mainCategory->id,
            'sub_category_id' => $this->subCategory->id,
            'purchase_price_per_kg' => 100,
            'selling_price_per_kg' => 150,
            'minimum_stock_alert' => 10,
        ];

        $response = $this->actingAs($this->user)->post('/products', $data);

        $response->assertSessionHasErrors('sku');
    }

    public function test_show_returns_product_json()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->get("/products/{$product->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_edit_shows_form()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->get("/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('products.edit');
    }

    public function test_update_modifies_product()
    {
        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Product',
            'name_ar' => 'منتج محدث',
            'sku' => $product->sku,
            'main_category_id' => $this->mainCategory->id,
            'sub_category_id' => $this->subCategory->id,
            'purchase_price_per_kg' => 120,
            'selling_price_per_kg' => 180,
            'minimum_stock_alert' => 15,
        ];

        $response = $this->actingAs($this->user)->patch("/products/{$product->id}", $data);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product'
        ]);
    }

    public function test_destroy_soft_deletes_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->user)->delete("/products/{$product->id}");

        $response->assertRedirect('/products');
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function test_low_stock_returns_products()
    {
        Product::factory()->create(['current_stock_kg' => 5, 'minimum_stock_alert' => 10]);
        Product::factory()->create(['current_stock_kg' => 50, 'minimum_stock_alert' => 10]);

        $response = $this->actingAs($this->user)->get('/products/1/low-stock');

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_adjust_stock_updates_product()
    {
        $product = Product::factory()->create(['current_stock_kg' => 50]);

        $response = $this->actingAs($this->user)->patch("/products/{$product->id}/adjust-stock", [
            'quantity' => 10,
            'reference' => 'manual_adjustment'
        ]);

        $response->assertRedirect('/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'current_stock_kg' => 60
        ]);
    }

    public function test_product_requires_authentication()
    {
        $response = $this->get('/products');

        $response->assertRedirect('/login');
    }

    public function test_product_requires_proper_authorization()
    {
        $user = User::factory()->create(['role_id' => 3]); // Different role

        $data = [
            'name' => 'Test',
            'name_ar' => 'اختبار',
            'sku' => 'TEST-001',
            'main_category_id' => $this->mainCategory->id,
            'sub_category_id' => $this->subCategory->id,
            'purchase_price_per_kg' => 100,
            'selling_price_per_kg' => 150,
            'minimum_stock_alert' => 10,
        ];

        $response = $this->actingAs($user)->post('/products', $data);

        $response->assertStatus(403);
    }
}
