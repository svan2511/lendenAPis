<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Onboarding extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_type',
        'has_stock',
        'has_appointments',
        'has_staff',
    ];

    protected $casts = [
        'has_stock'       => 'boolean',
        'has_appointments' => 'boolean',
        'has_staff'       => 'boolean',
    ];

     protected $hidden = [
        'created_at','updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
