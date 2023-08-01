<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card_Status extends Model
{
    use HasFactory;

    protected $fillable = ['title'];

    public function cards()
    {
        return $this->hasMany(Card::class,'status_id');
    }

    protected $table = 'cardStatus'; 
}
