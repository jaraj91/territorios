<?php

namespace App\Models;

use App\Enums\Months;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'month' => Months::class,
    ];

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class);
    }
}
