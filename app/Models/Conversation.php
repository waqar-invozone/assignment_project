<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function add($data) {
        return self::create([
            'title' => $data['title'],
            'senderId' => $data['participants'][0],
            'receiverId' => $data['participants'][1],
        ])->id;
    }

    public function messages() {
        return $this->hasMany(Message::class)->latest()->limit(10);
    }

    public function receiver() {
        return $this->belongsTo(Contact::class, 'receiverId', 'id');
    }

    public function sender() {
        return $this->belongsTo(Contact::class, 'senderId', 'id');
    }
}
