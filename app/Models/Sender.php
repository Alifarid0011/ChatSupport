<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sender extends Model
{
    use HasFactory;

    protected $guarded = [];
    public $timestamps = false;
    public function user()
    {
        return $this->morphTo();
    }
    public function messages()
    {
        return $this->belongsTo(Message::class,'message_id','id');
    }
    public function receiver()
    {
        return $this->belongsTo(Receiver::class);
    }

}
