<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
     protected $fillable = [
        'senderId',
        'receiverId',
        'amount',
    ];
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'senderId');
    }
     public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiverId');
    }
}
