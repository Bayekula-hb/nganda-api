<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class userRole extends Model
{
    use HasFactory;    /**
    * The attributes that are mass assignable.
    *
    * @var array<int, string>
    */
   protected $fillable = [
       'nameRole',
       'descriptionRole',
   ];

   
        
   public function userRoleTab(): BelongsToMany
   {
       return $this->BelongsToMany(userRoleTab::class, 'user_role_tabs');
   }

}
