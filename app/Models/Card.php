<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'status_id'];

    public function card_status()
    {
        return $this->belongsTo(Card_Status::class);
    }

    protected $table = 'cards'; 
}
