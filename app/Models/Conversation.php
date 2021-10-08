<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;

    public function messages() {
        return $this->hasMany(Message::class)->latest()->limit(10);
    }

    public function receiverContact() {
        return $this->belongsTo(Contact::class, 'receiver');
    }

    public function senderContact() {
        return $this->belongsTo(Contact::class, 'sender');
    }

}
