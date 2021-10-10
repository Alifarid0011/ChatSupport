<?php

namespace App\Models;

use App\traits\Shamsi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, Shamsi, SoftDeletes;

    protected $guarded = [];

//    protected $fillable=['message','support_id','receive_id'];
//    public function user()
//    {
//        return $this->belongsTo(User::class);
//    }
    public function sender()
    {
        return $this->hasOne(Sender::class,'user_id','id');
    }
    public function receiver()
    {
        return $this->hasOne(Receiver::class,'user_id','id');
    }

    protected $appends = ['make_at'];

}
