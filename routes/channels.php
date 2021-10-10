<?php

//use Illuminate\Auth\Authenticatable;
use App\Models\Guest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});
//Broadcast::channel('App.Models.Guest.{id}', function ($Guest, $id) {
//    return (int)$Guest->id === (int)$id;
//});

Broadcast::channel('Chat', function ($user) {
    return [
        'name' => $user->name,
        'id' => $user->id,
//        'support_id'=>$user->support,
        'role' => $user->getRoleNames()[0],
    ];
});
Broadcast::channel('ChatGuest', function (Authenticatable $user) {
    if (auth()->user()) {
        return [
           'isGuest'=> false,
//            'support_id'=>false
        ];
    }
    $token=$user->getAuthIdentifier();
    $guest = Guest::query()->Where('cookie',$token)->with('support')->first();
    return [
        'id' => $guest->id,
        'token' => $token,
        'name' => $user->name ."_". $guest->id,
        'isGuest'=> true,
//        'support_id'=>$guest->support,
        "type"=>"Guest"
    ];
}, ['guards' => ['guest']]);
