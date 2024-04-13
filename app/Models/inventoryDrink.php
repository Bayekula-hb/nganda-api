<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class inventoryDrink extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'price',
        'drink_id',
        'establishment_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function sale(): HasMany
    {
        return $this->hasMany(sale::class, 'sales');
    }
            
    public function users(): BelongsTo
    {
        return $this->belongsTo(drink::class);
    }

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(establishment::class);
    }
}
