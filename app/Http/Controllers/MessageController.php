<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    use ZoomMeetingTrait;

    public function index($id) {
        $data = Message::selectRaw('messages.*, contacts.name as senderName')
            ->join('conversations', 'conversations.id', 'messages.conversation_id')
            ->join('contacts', 'contacts.id', 'conversations.sender')
            ->where('conversation_id', $id)->get();
        return response()->json(
            [
                'list' => $data->all(),
                'totalPages' => $data->lastPage(),
                'currentPage' => $data->currentPage(),
            ]
        );
    }

    public function store(Request $request, $id) {
        $validation = Validator::make($request->all(), [
            'content' => 'required|string',
            'type' => 'required|string', // either "Meeting" or "Text"
            'senderId' => 'required|numeric',
        ]);

        if ($validation->fails()) {
            return response()->json($validation->messages(), 422);
        }
        $data = $request->all();

        if ($data['type'] == 'Meeting') {
            $path = 'users/me/meetings';
            $response = $this->zoomPost($path, [
                'topic' => $data['title'],
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat(Carbon::now()),
                'duration' => 30,
                'agenda' => $data['agenda'] ?? '',
                'settings' => [
                    'host_video' => false,
                    'participant_video' => false,
                    'waiting_room' => true,
                ],
            ]);
            $response = json_decode($response->body(), true);

            return response()->json(
                [
                    'zoom_details' => $response,
                ]
            );
        }


        $path = '/chat/users/me/channels';
        $response = $this->zoomPost($path, [
            'name' => $data['title'],
            'type' => 3,
        ]);

        $path2 = '/chat/users/me/messages';
        $response2 = $this->zoomPost($path, [
            'name' => $data['title'],
            'type' => 3,
        ]);

        $response = json_decode($response->body(), true);

        $message = Message::create([
            'type' => $data['type'],
            'content' => $data['content'],
            'conversation_id' => $id,
        ]);

        return response()->json(
            [
                'id' => $message->id,
                'zoom_details' => $response,
            ]

        );
    }

    public function show($id, $msg_id) {
        $message = Message::selectRaw('messages.content, conversations.sender as senderId, contacts.name as senderName, messages.created_at as createdAt')
            ->join('conversations', 'conversations.id', 'messages.conversation_id')
            ->join('contacts', 'contacts.id', 'conversations.sender')
            ->where('conversation_id', $id)
            ->where('messages.id', $msg_id)
            ->first();
        return response()->json($message);
    }
}
