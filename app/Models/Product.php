<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory , SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'price',
        'quantity',
        'type',
        'unit_type'
    ];

    protected $casts = [
        'price'    => 'decimal:2',
        'quantity' => 'integer',
        'type'     => 'string',
        'unit_type'=> 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper: is this a service?
    public function isService(): bool
    {
        return $this->type === 'service';
    }

    // Helper: is this sold by weight?
    public function isWeightBased(): bool
    {
        return $this->unit_type === 'weight';
    }

    public function decreaseStock(float $quantity): void
    {
        if ($this->quantity < $quantity) {
            throw new \Exception("Insufficient stock for product {$this->name} (ID: {$this->id}). Requested: {$quantity}, Available: {$this->quantity}");
        }

        $this->decrement('quantity', $quantity);

        // Optional: can dispatch low-stock event / notification here later
    }
}