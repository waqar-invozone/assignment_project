<?php

namespace Tests\Feature;

use Tests\TestCase;

class MessagesTest extends TestCase
{


    public function test_store() {

        $response = $this->post('/api/conversations/1/messages', []);
        $response->assertStatus(422);


        $response = $this->post('/api/conversations/1/messages', [
            'content' => 'message',
            'type' => 'Meeting',
            'senderId' => 1,
        ]);

        $response->assertStatus(200)->assertJson(['zoom_details' => true]);


        $response = $this->post('/api/conversations/1/messages', [
            'content' => 'message',
            'type' => 'Text',
            'senderId' => 1,
        ]);

        $response->assertStatus(200)->assertJson(['id' => true, 'zoom_details' => true]);

    }

    public function test_list() {
        $response = $this->get('/api/conversations/1/messages');
        $response->assertStatus(200);
    }

    public function test_get() {
        $response = $this->get('/api/conversations/1/messages/1');

        $response->assertStatus(200);

    }
}
