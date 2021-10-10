<?php

namespace App\Models;

use App\traits\Shamsi;
use Hekmatinasser\Verta\Verta;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles,Shamsi;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
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
    protected $appends = ['make_at'];
}
