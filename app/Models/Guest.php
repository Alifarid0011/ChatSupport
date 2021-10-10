<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    use HasFactory;
    protected $fillable=['cookie'];
    public function ban()
    {
        return  $this->morphMany(Ban::class,'bannable');
    }

    public function senders()
    {
        return  $this->morphMany(Sender::class,'user');
    }
    public function receivers()
    {
        return  $this->morphMany(Receiver::class,'user');
    }
    public function support()
    {
        return  $this->morphMany(Support::class,'user');
    }

}
