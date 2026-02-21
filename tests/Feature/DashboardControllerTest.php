<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role_id' => 1]);
    }

    public function test_dashboard_index_returns_view_with_data()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index');
        $response->assertViewHas('stats');
    }

    public function test_dashboard_displays_statistics()
    {
        Invoice::factory()->count(3)->create(['type' => 'regular']);
        Invoice::factory()->count(2)->create(['type' => 'quick']);
        Client::factory()->count(5)->create();
        Product::factory()->count(10)->create();
        Purchase::factory()->count(2)->create(['status' => 'pending']);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('stats', function ($stats) {
            return isset($stats['total_invoices']) &&
                   isset($stats['quick_sales']) &&
                   isset($stats['total_clients']) &&
                   isset($stats['total_products']) &&
                   isset($stats['pending_purchases']);
        });
    }

    public function test_sales_report_with_date_range()
    {
        $response = $this->actingAs($this->user)->get('/reports/sales', [
            'start_date' => now()->subDays(30)->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('reports.sales');
    }

    public function test_profit_report_returns_view()
    {
        $response = $this->actingAs($this->user)->get('/reports/profit');

        $response->assertStatus(200);
        $response->assertViewIs('reports.profit');
    }

    public function test_inventory_report_returns_view()
    {
        $response = $this->actingAs($this->user)->get('/reports/inventory');

        $response->assertStatus(200);
        $response->assertViewIs('reports.inventory');
    }

    public function test_daily_closing_report_returns_view()
    {
        $response = $this->actingAs($this->user)->get('/reports/daily-closing');

        $response->assertStatus(200);
        $response->assertViewIs('reports.daily-closing');
    }

    public function test_daily_closing_report_with_specific_date()
    {
        $date = now()->subDays(5)->toDateString();

        $response = $this->actingAs($this->user)->get('/reports/daily-closing', [
            'date' => $date,
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('reports.daily-closing');
    }

    public function test_dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
