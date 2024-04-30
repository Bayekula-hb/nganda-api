<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class drink extends Model
{
    use HasFactory;

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
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function inventoryDrink(): HasMany
    {
        return $this->hasMany(inventoryDrink::class, 'inventory_drinks');
    }

}
