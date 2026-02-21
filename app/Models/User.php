<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get user's role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get products created by this user
     */
    public function products()
    {
        return $this->hasMany(Product::class, 'created_by');
    }

    /**
     * Get invoices created by this user
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Get purchases created by this user
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    /**
     * Get expenses created by this user
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    /**
     * Get clients created by this user
     */
    public function clients()
    {
        return $this->hasMany(Client::class, 'created_by');
    }

    /**
     * Get stock movements created by this user
     */
    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'created_by');
    }

    /**
     * Get invoice payments created by this user
     */
    public function invoicePayments()
    {
        return $this->hasMany(InvoicePayment::class, 'created_by');
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Check if user is admin
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is sales
     */
    public function isSales()
    {
        return $this->hasRole('sales');
    }

    /**
     * Check if user is accountant
     */
    public function isAccountant()
    {
        return $this->hasRole('accountant');
    }
}
