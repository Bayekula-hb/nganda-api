<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class establishment extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'isOnDemonstration',
        'isActive',
        'subscriptionExpiryDate',
        'settings',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
            
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sale(): HasMany
    {
        return $this->hasMany(sale::class, 'sales');
    }

    public function payment(): HasMany
    {
        return $this->hasMany(sale::class, 'payments');
    }
    
    public function inventoryDrink(): HasMany
    {
        return $this->hasMany(inventoryDrink::class, 'inventory_drinks');
    }
}
