<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class sale extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'user_id',
        'establishment_id',
        'inventory_drink_id',
    ];

            
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(establishment::class);
    }

    public function inventoryDrink(): BelongsTo
    {
        return $this->belongsTo(inventoryDrink::class);
    }
}
