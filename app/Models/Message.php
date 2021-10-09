<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function list($id) {
        return self::selectRaw('messages.*, contacts.name as senderName')
            ->join('conversations', 'conversations.id', 'messages.conversation_id')
            ->join('contacts', 'contacts.id', 'conversations.senderId')
            ->where('messages.conversation_id', $id)->paginate(10);
    }

    public static function add($data, $id) {
        return self::create([
            'type' => $data['type'],
            'content' => $data['content'],
            'conversation_id' => $id,
        ])->id;
    }

    public static function show($c_id,$id) {
        return self::selectRaw('messages.content, conversations.senderId, contacts.name as senderName, messages.created_at as createdAt')
            ->join('conversations', 'conversations.id', 'messages.conversation_id')
            ->join('contacts', 'contacts.id', 'conversations.senderId')
            ->where('conversation_id', $c_id)
            ->where('messages.id', $id)
            ->first();
    }
}
