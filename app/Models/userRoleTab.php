<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class userRoleTab extends Model
{
    use HasFactory, SoftDeletes;

        /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'user_role_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

        
    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function userRoles(): BelongsTo
    {
        return $this->belongsTo(userRole::class);
    }
}
