<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneBookName extends Model
{
    use HasFactory;
    protected $table = "phone_book_names";
    protected $fillable = ["name", "ruby"];

    public function numbers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PhoneBookPhoneNumber::class, "name_id", "id");
    }

    public function version(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PhoneBookVersion::class);
    }
}
