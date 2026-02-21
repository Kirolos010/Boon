<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
        $this->category = ExpenseCategory::factory()->create();
    }

    public function test_index_lists_expenses()
    {
        Expense::factory()->count(10)->create(['expense_category_id' => $this->category->id]);

        $response = $this->actingAs($this->user)->get('/expenses');

        $response->assertStatus(200);
        $response->assertViewIs('expenses.index');
        $response->assertViewHas('expenses');
    }

    public function test_create_shows_form()
    {
        $response = $this->actingAs($this->user)->get('/expenses/create');

        $response->assertStatus(200);
        $response->assertViewIs('expenses.create');
    }

    public function test_store_creates_expense()
    {
        $data = [
            'expense_category_id' => $this->category->id,
            'description' => 'Office supplies',
            'amount' => 250.50,
            'expense_date' => now()->toDateString(),
            'notes' => 'Monthly office supplies',
        ];

        $response = $this->actingAs($this->user)->post('/expenses', $data);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', [
            'description' => 'Office supplies',
            'amount' => 250.50,
            'created_by' => $this->user->id
        ]);
    }

    public function test_store_fails_without_required_fields()
    {
        $data = [
            'description' => 'Office supplies',
            // Missing amount, expense_date, category
        ];

        $response = $this->actingAs($this->user)->post('/expenses', $data);

        $response->assertSessionHasErrors(['expense_category_id', 'amount', 'expense_date']);
    }

    public function test_store_fails_with_invalid_category()
    {
        $data = [
            'expense_category_id' => 9999,  // Non-existent category
            'description' => 'Office supplies',
            'amount' => 250.50,
            'expense_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)->post('/expenses', $data);

        $response->assertSessionHasErrors('expense_category_id');
    }

    public function test_show_returns_expense_json()
    {
        $expense = Expense::factory()->create(['expense_category_id' => $this->category->id]);

        $response = $this->actingAs($this->user)->get("/expenses/{$expense->id}");

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
    }

    public function test_edit_shows_form()
    {
        $expense = Expense::factory()->create(['expense_category_id' => $this->category->id]);

        $response = $this->actingAs($this->user)->get("/expenses/{$expense->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('expenses.edit');
    }

    public function test_update_modifies_expense()
    {
        $expense = Expense::factory()->create(['expense_category_id' => $this->category->id]);

        $data = [
            'expense_category_id' => $this->category->id,
            'description' => 'Updated supplies',
            'amount' => 500.00,
            'expense_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)->patch("/expenses/{$expense->id}", $data);

        $response->assertRedirect('/expenses');
        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'description' => 'Updated supplies',
            'amount' => 500.00
        ]);
    }

    public function test_destroy_deletes_expense()
    {
        $expense = Expense::factory()->create(['expense_category_id' => $this->category->id]);

        $response = $this->actingAs($this->user)->delete("/expenses/{$expense->id}");

        $response->assertJson(['status' => 'success']);
        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }

    public function test_expense_requires_authentication()
    {
        $response = $this->get('/expenses');

        $response->assertRedirect('/login');
    }

    public function test_store_fails_with_zero_amount()
    {
        $data = [
            'expense_category_id' => $this->category->id,
            'description' => 'Office supplies',
            'amount' => 0,
            'expense_date' => now()->toDateString(),
        ];

        $response = $this->actingAs($this->user)->post('/expenses', $data);

        $response->assertSessionHasErrors('amount');
    }

    public function test_store_records_user_id_correctly()
    {
        $data = [
            'expense_category_id' => $this->category->id,
            'description' => 'Office supplies',
            'amount' => 250.50,
            'expense_date' => now()->toDateString(),
        ];

        $this->actingAs($this->user)->post('/expenses', $data);

        $this->assertDatabaseHas('expenses', [
            'created_by' => $this->user->id
        ]);
    }
}
