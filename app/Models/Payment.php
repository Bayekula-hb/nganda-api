<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'establishment_id',
        'amount',
        'payment_method',
        'number_month',
        'status',
        'ref_flexpay',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];    

    public function establishment(): BelongsTo
    {
        return $this->belongsTo(establishment::class);
    }
}
