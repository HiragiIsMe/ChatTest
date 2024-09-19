<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Http\Requests\MakeChatRequest;
use App\Http\Requests\MakeMessageRequest;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

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

    public function getAllMessage(int $id)
    {
        $data = DB::table('conversations')
                    ->join('messages', 'conversations.id', '=', 'messages.conversation_id')
                    ->select('messages.*')
                    ->where('conversations.id', '=', $id)
                    ->get();

        return response()->json(["Message" => "Success Get Data Of Conversation '$id'","data" => $data]);
    }
}
