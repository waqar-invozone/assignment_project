<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Message;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Traits\ZoomMeetingTrait;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{

    use ZoomMeetingTrait;

    public function index($id) {
        $data = Message::list($id);
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
        $conversation = Conversation::findOrFail($id);

        if ($data['type'] == 'Meeting') {
            $path = 'users/me/meetings';
            $response = $this->zoomPost($path, [
                'topic' => $conversation->title,
                'type' => self::MEETING_TYPE_SCHEDULE,
                'start_time' => $this->toZoomTimeFormat(Carbon::now()),
                'duration' => 30,
                'agenda' => 'Meeting',
                'settings' => [
                    'host_video' => false,
                    'participant_video' => false,
                    'waiting_room' => true,
                ],
            ]);
            $response = json_decode($response->body(), true);

            return response()->json(
                [
                    Message::add($data, $id),
                    'zoom_details' => $response,
                ]
            );
        }


        $path = '/chat/users/me/channels';
        $response = $this->zoomPost($path, [
            'name' => $conversation->title,
            'type' => 3,
        ]);

        $path2 = '/chat/users/me/messages';
        $response2 = $this->zoomPost($path, [
            'name' => $conversation->title,
            'type' => 3,
        ]);

        $response = json_decode($response->body(), true);


        return response()->json(
            [
                'id' => Message::add($data, $id),
                'zoom_details' => $response,
            ]

        );
    }

    public function show($c_id, $id) {
        return response()->json(Message::show($c_id, $id));
    }
}
