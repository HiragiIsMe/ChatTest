<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MakeChatRequest;
use App\Http\Requests\MakeMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ChatsController extends Controller
{
    public function MakeChat(MakeChatRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $chat = Conversation::create([
            'user_1' => $data['user_1'],
            'user_2' => $data['user_2']
        ]);

        return response()->json(["message" => "success", "id" => $chat->id]);
    }

    public function MakeMessage(MakeMessageRequest $request)
    {
        $data = $request->validated();

        $message = Message::create([
            'conversation_id' => $data['conversation_id'],
            'sender_id' => $data['sender_id'],
            'message' => $data['message']
        ]);

        event(new MessageSent($message));

        return response()->json(['message' => 'Message has been send'], 200);
    }

    public function AllChat()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $conversations = DB::table('conversations')
            ->where(function($query) use ($user) {
                $query->where('user_1', $user->id)
                    ->orWhere('user_2', $user->id);
            })
            ->leftJoin('users as user_one', 'conversations.user_1', '=', 'user_one.id')
            ->leftJoin('users as user_two', 'conversations.user_2', '=', 'user_two.id')
            ->leftJoin('messages', function($join) {
                $join->on('conversations.id', '=', 'messages.conversation_id')
                    ->whereRaw('messages.created_at = (select max(created_at) from messages where messages.conversation_id = conversations.id)');
            })
            ->select(
                'conversations.id',
                DB::raw('IF(conversations.user_1 = ' . $user->id . ', user_two.id, user_one.id) as user_id'),
                DB::raw('IF(conversations.user_1 = ' . $user->id . ', user_two.username, user_one.username) as username'),
                DB::raw('IF(conversations.user_1 = ' . $user->id . ', user_two.name, user_one.name) as name'),
                'messages.message as last_message',
                'messages.created_at as updated_at'
            )
            ->groupBy('conversations.id', 'user_id', 'username', 'name', 'last_message', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        return response()->json([
            'chats' => $conversations
        ]);
    }

    public function getAllMessage(int $id)
    {
        $data = DB::table('conversations')
                ->join('messages', 'conversations.id', '=', 'messages.conversation_id')
                ->select('messages.*')
                ->where('conversations.id', '=', $id)
                ->orderBy('messages.created_at', 'desc') 
                ->get();

        return response()->json(["Message" => "Success Get Data Of Conversation '$id'","data" => $data]);
    }

    public function GetAllUsers()
    {
        $loggedInUserId = Auth::user()->id;

        $users = User::where('id', '!=', $loggedInUserId)->get();
    
        return response()->json(["message" => "Success", "data" => $users]);
    }
}
