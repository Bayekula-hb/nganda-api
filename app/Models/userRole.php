<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class userRole extends Model
{
    use HasFactory, SoftDeletes;    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
       'nameRole',
       'descriptionRole',
   ];

   
   protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
   ];
        
   public function userRoleTab(): BelongsToMany
   {
       return $this->BelongsToMany(userRoleTab::class, 'user_role_tabs');
   }

}
