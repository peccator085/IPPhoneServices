<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneBookPhoneNumber extends Model
{
    use HasFactory;
    protected $fillable = ["type", "number"];
    protected $table = "phone_book_numbers";

    public function name(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(PhoneBookName::class);
    }
}
