<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class saleStore extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'quantity',
        'user_id',
        'establishment_id',
        'inventory_store_id',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

            
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function establishment(): BelongsTo
    {
        return $this->belongsTo(establishment::class);
    }

    public function inventoryStore(): BelongsTo
    {
        return $this->belongsTo(inventoryStore::class);
    }
}
