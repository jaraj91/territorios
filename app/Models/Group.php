<?php

namespace App\Models;

use App\Enums\GroupStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Group extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'progress' => 'array'
    ];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(Captain::class);
    }

    public function territory(): BelongsTo
    {
        return $this->belongsTo(Territory::class);
    }

    public function status(): Attribute
    {
        return Attribute::make(
            get: static function (mixed $value, array $attributes) {
                return match($attributes['progress']) {
                    null => GroupStatus::Pending,
                    default => GroupStatus::Registered,
                };
            }
        );
    }
}
