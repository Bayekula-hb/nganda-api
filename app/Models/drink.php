<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class drink extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nameDrink',
        'imageDrink',
        'litrage',
        'typeDrink',
        'priorityDrink',
        'numberBottle',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function inventoryDrink(): HasMany
    {
        return $this->hasMany(inventoryDrink::class, 'inventory_drinks');
    }
    public function inventoryDrinkStore(): HasMany
    {
        return $this->hasMany(inventoryStore::class, 'inventory_stores');
    }

}
