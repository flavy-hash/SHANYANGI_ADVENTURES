<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripRequest extends Model
{
    public const INTERESTS = [
        'safari' => 'Safari',
        'kilimanjaro' => 'Kilimanjaro',
        'zanzibar' => 'Zanzibar',
        'day-trips' => 'Day trips',
        'culture' => 'Cultural experiences',
        'honeymoon' => 'Honeymoon',
    ];

    public const BUDGETS = [
        'budget' => 'Budget',
        'mid-range' => 'Mid-range',
        'luxury' => 'Luxury',
        'not-sure' => 'Not sure yet',
    ];

    protected $fillable = [
        'name', 'email', 'phone', 'country', 'trip', 'travel_date', 'duration_days',
        'adults', 'children', 'interests', 'budget', 'message', 'handled_at',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'interests' => 'array',
            'handled_at' => 'datetime',
        ];
    }
}
