<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class establishment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nameEtablishment',
        'latitude',
        'longitude',
        'address',
        'pos',
        'numberPos',
        'workers',
        'workingDays',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
            
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): HasMany
    {
        return $this->hasMany(sale::class, 'sales');
    }
    
    public function inventoryDrink(): HasMany
    {
        return $this->hasMany(inventoryDrink::class, 'inventory_drinks');
    }
}
