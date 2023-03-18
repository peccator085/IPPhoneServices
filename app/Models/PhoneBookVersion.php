<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneBookVersion extends Model
{
    use HasFactory;
     protected $fillable = [];
     protected $table = "phone_book_versions";

     public function names(): \Illuminate\Database\Eloquent\Relations\HasMany
     {
         return $this->hasMany(PhoneBookName::class, "version_id", "id");
     }


}
