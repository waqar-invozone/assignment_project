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
            ->join('contacts as sender', 'sender.id', 'conversations.sender')
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

        $data = $request->all();


        return response()->json(
            Conversation::insertGetId([
                'title' => $data['title'],
                'sender' => $data['participants'][0],
                'receiver' => $data['participants'][1],
            ])
        );
    }

    public function show(Request $request, $id) {
        $data = Conversation::select('conversations.*', 'sender.name as senderName')
            ->join('contacts as sender', 'sender.id', 'conversations.sender')
            ->where('id', $id)
            ->with(['messages'])->first();


        $data->participants = [
            $this->receiverContact,
            $this->senderContact,
        ];
        return response()->json($data);
    }
}
