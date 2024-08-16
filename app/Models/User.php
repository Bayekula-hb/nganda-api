<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'lastName',
        'middleName',
        'firstName',
        'userName',
        'gender',
        'phoneNumber',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

        
    public function userRoleTab(): BelongsToMany
    {
        return $this->BelongsToMany(userRoleTab::class, 'user_role_tabs');
    }

    public function sale(): HasMany
    {
        return $this->hasMany(sale::class, 'sales');
    }

    public function saleStore(): HasMany
    {
        return $this->hasMany(saleStore::class, 'sale_stores');
    }

    public function establishment(): HasMany
    {
        return $this->hasMany(establishment::class, 'establishments');
    }

    public function inventoryStore(): HasMany
    {
        return $this->hasMany(inventoryStore::class, 'inventory_stores');
    }

    public function inventoryDrink(): HasMany
    {
        return $this->hasMany(inventoryDrink::class, 'inventory_drinks');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
