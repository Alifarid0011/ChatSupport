<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receiver extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;
    public function user()
    {
        return $this->morphTo();
    }
    public function sender()
    {
        return $this->hasOne( Sender::class);
    }
    public function messages()
    {
        return $this->belongsTo(Message::class);
    }

}
