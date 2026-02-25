<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'amount',
        'expense_date',
        'category',
        'payment_mode',
    ];

    protected $casts = [
        'expense_date' => 'date:Y-m-d',
        'amount'       => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}