<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ConversationController extends Controller
{
    use ZoomMeetingTrait;

    public function index() {
        $data = Conversation::selectRaw('conversations.id, conversations.title, sender.name as senderName, messages.content as lastMessage')
            ->join('contacts as sender', 'sender.id', 'conversations.senderId')
            ->leftJoin(DB::raw('(select * from messages order by created_at desc limit 1) as messages'), 'messages.conversation_id', 'conversations.id')
            ->paginate(10);
        return response()->json(
            [
                'list' => $data->all(),
                'totalPages' => $data->lastPage(),
                'currentPage' => $data->currentPage(),
            ]
        );
    }

    public function store(Request $request) {

        $validation = Validator::make($request->all(), [
            'title' => 'required|string',
            'participants' => 'required|array|min:2|max:2',
            'participants.*' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->messages(), 422);
        }


        return response()->json([
            'id' => Conversation::add($request->all()),
        ]);
    }

    public function show(Request $request, $id) {
        $data = Conversation::select('conversations.id', 'conversations.title', 'conversations.senderId','conversations.receiverId', 'sender.name as senderName')
            ->join('contacts as sender', 'sender.id', 'conversations.senderId')
            ->where('conversations.id', $id)
            ->with(['messages','receiver','sender'])->first();


        $data->participants = [
            $data->receiver,
            $data->sender,
        ];
        return response()->json($data);
    }
}
