<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'name',
        'name_ar',
        'description',
    ];

    /**
     * Get expenses in this category
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
