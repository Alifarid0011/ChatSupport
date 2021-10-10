<?php

namespace App\Http\Controllers;

use App\Events\SendMessage;
use App\Models\Guest;
use App\Models\Message;
use App\Models\Rate;
use App\Models\Sender;
use App\Models\Support;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;


class MessageController extends Controller
{
//    نمایش
    public function index()
    {
        return view('Parts.chat');
    }

//ارسال پیام
    public function sendMessage(Request $request)
    {
        $message = Message::create([
            'Message' => $request->message
        ]);
        $receiver = null;
        switch ($request->role) {
            case 'guest':
                $guest = Guest::query()->find($request->guest_id);
                if (is_null($request->support_id)) {
                    $guest->senders()->create([
                        'message_id' => $message->id,
                        'receiver_id' => null,
                    ]);
                } else {
                    $user = User::query()->find($request->support_id);
                    $receiver = $user->receivers()->create([
                        'message_id' => $message->id
                    ]);
                    $guest->senders()->create([
                        'message_id' => $message->id,
                        'receiver_id' => $receiver->id,
                    ]);
                }
                break;
            case 'user':
                $user = User::query()->find($request->user_id);
                if (is_null($request->support_id)) {
                    $user->senders()->create([
                        'message_id' => $message->id,
                        'receiver_id' => null,
                    ]);
                } else {
                    $support = User::query()->find($request->support_id);
                    $receiver = $support->receivers()->create([
                        'message_id' => $message->id
                    ]);
                    $user->senders()->create([
                        'message_id' => $message->id,
                        'receiver_id' => $receiver->id,
                    ]);
                }
                break;
            case 'support':
                $user = User::query()->find($request->support_id);
                if ($request->receiver_role === "User") {
                    $recive = User::query()->find($request->receiver_id);
                    $receiver = $recive->receivers()->create([
                        'message_id' => $message->id
                    ]);
                } elseif ($request->receiver_role === "Guest") {
                    $guest = Guest::query()->find($request->receiver_id);
                    $receiver = $guest->receivers()->create([
                        'message_id' => $message->id
                    ]);
                }
                $user->senders()->create([
                    'message_id' => $message->id,
                    'receiver_id' => $receiver->id,
                ]);
                break;
        }
        broadcast(new SendMessage($message));
        return ['status' => 'success'];
    }
    //دریافت پیام
    public function fetchMessage(Request $request)
    {
        $support_id = $request->support_id;
        $user_id = $request->user_id;
        $role = $request->role;
        $sender = Sender::where(function ($query) use ($user_id, $role) {
            $query->where('user_type', $role)
                ->where('user_id', $user_id);
        })->OrWhere(function ($query) use ($support_id) {
            $query->where('user_type', "User")
                ->where('user_id', $support_id);
        })->with(['messages', 'receiver' => function ($q) use ($role, $user_id, $support_id) {
            $q->where(function ($query) use ($role, $user_id) {
                $query->where('user_type', $role)
                    ->where('user_id', $user_id);
            })->OrWhere(function ($query) use ($support_id) {
                $query->where('user_type', "User")
                    ->where('user_id', $support_id);
            });
        },])
            ->get()->toArray();
        $res = [];
        foreach ($sender as $key => $item) {
            if ($item['user_type'] === "User" && $item['user_id'] == $support_id) {
                if ($item['receiver'] === null) {
                } else {
                    $res[$key] = $item;
                    array_push($res[$key], ["is_support_user" => true]);
                }
            } else {
                $res[$key] = $item;
                array_push($res[$key], ["is_support_user" => false]);
            }
        }
        return $res;
    }

//    ارسال امتیاز کاربران ثبت نام شده
    public function rateToSupport(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'support_id' => 'required',
            'rate' => 'required|integer',
        ]);
        Rate::create([
            'support_id' => $request->support_id,
            'user_id' => $request->user_id,
            'Rate' => $request->rate
        ]);
        return response()->json([
            'message' => "rate were recorded"
        ], Response::HTTP_OK);
    }

//کاربرانی که توسط یک پشتیبان پشتیبانی میشوند

    public function supports(Request $request)
    {
        $res = [];
        $users = Support::where('support_id', $request->support_id)->with('user.support')->get()->toArray();
        foreach ($users as $item) {
            if ($item["user_type"] === "User") {
                $res[] = [
                    "id" => $item["user"]["id"],
                    "name" => $item["user"]["name"],
                    "type" => "User",
//                "support_id"=>$item["user"]["support"],
                    "online" => false
                ];
            } else {
                $res[] = [
                    "id" => $item["user"]["id"],
                    "name" => $item["user"]["id"] . "_مهمان",
                    "type" => "Guest",
//                    "support_id"=>$item["user"]["support"],
                    "online" => false
                ];
            }
        }

        return response()->json([
            $res
        ]);
    }

//    نمایش کاربر
    public function showUser()
    {
        if (Auth::user()) {
            $user = Auth::user();
            return response()->json([
                'user' => $user->name,
                'id' => $user->id,
                'Role' => $user->getRoleNames(),
                'Ban' => $user->ban()
            ]);
        }
        $token = Cookie::get('Chat_Cookie');
        $guest = Guest::whereCookie($token)->first();
        return response()->json([
            'id' => $guest->id,
            'Ban' => $guest->ban()
        ]);


    }

//    انتخاب پشتیبانی برای کاربر
    public function selectSupportForUser(Request $request)
    {
        $supportable = Support::where([
            'user_type' => $request->role,
            'user_id' => $request->user_id
        ])->first();
        switch ($request->role) {
            case 'Guest':
                if (is_null($supportable)) {
                    $guest = Guest::query()->findOrFail($request->user_id);
                    $guest->support()->create([
                        'support_id' => $request->support_id
                    ]);
                }
                break;
            case 'User':
                if (is_null($supportable)) {
                    $user = User::query()->findOrFail($request->user_id);
                    $user->support()->create([
                        'support_id' => $request->support_id
                    ]);
                }
                break;
        }
    }

//چک کردن ارتباط بین کاربران و پشتیبانی
    public function supportable(Request $request)
    {
        $role = $request->role;
        $supportable = Support::where([
            'user_type' => $role,
            'user_id' => $request->user_id
        ])->first();
        if (is_null($supportable)) {
            return response()->json([
                false
            ]);
        }
        return response()->json([
            true,
            $supportable->support_id
        ]);
    }

//بن کردن کاربران
    public function ban(Request $request)
    {

        switch ($request->type) {
            case 'Auto':

                break;
            case 'Guest':
                $guest = Guest::query()->whereId($request->id)->get();
                if ($request->bySupport) {
                    $guest->ban()->create([
                        'user_id' => $request->bySupport
                    ]);
                }
                $guest->ban()->create();
                break;
            default:
                $user = User::query()->whereId($request->id)->get();
                $user->ban()->create();
                break;
        }
    }

//پایان چت
    public function endChat(Request $request)
    {
        $support = $request->support_id;
        $receive = $request->receive_id;
        $messages = Message::query()
            ->with('user.roles')
            ->where('support_id', $support)
            ->Where('receive_id', $receive);
        return $messages->delete();
    }


}
