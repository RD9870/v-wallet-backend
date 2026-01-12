<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferRequest  extends Model
{

    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'sender_id',
        'receiver_id',
        'amount',
        'status',
        'expires_at'
    ];
}
