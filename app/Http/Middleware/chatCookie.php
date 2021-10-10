<?php

namespace App\Http\Middleware;

use App\Models\Guest;
use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class chatCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guest()) {
            $minutes = 10080;
//                dd(Cookie::get('Chat_Cookie'));
            if (Cookie::get('Chat_Cookie') === null) {
                $TOKEN = uniqid("AF_", 2);
                $TOKEN .= MD5(rand());
                $TOKEN .= Sha1(rand());
                Guest::create([
                    'cookie'=>$TOKEN
                ]);
            return $next($request)->withCookie(cookie('Chat_Cookie',$TOKEN, $minutes,null, null, false, false));
            }
        }
            return $next($request);
    }
}
