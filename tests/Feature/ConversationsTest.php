<?php

namespace Tests\Feature;

use Tests\TestCase;

class ConversationsTest extends TestCase
{
    public function test_store() {

        $response = $this->post('/api/conversations', [
            "title" => "test",
        ]);
        $response->assertStatus(422);

        $response = $this->post('/api/conversations', [
            "participants" => [1, 2],
        ]);
        $response->assertStatus(422);

        $response = $this->post('/api/conversations', [
            "title" => "test",
            "participants" => [1, 2],
        ]);

        $response->assertStatus(200)->assertJson(['id' => true]);

    }

    public function test_list() {
        $response = $this->get('/api/conversations');

        $response->assertStatus(200)
            ->assertJson([
                'list' => true,
                'totalPages' => true,
                'currentPage' => true,
            ]);

    }


    public function test_get() {
        $response = $this->get('/api/conversations/1');

        $response->assertStatus(200)
            ->assertJson([
                'title' => true,
                'senderId' => true,
                'senderName' => true,
                'participants' => true,
            ]);
        $this->assertTrue(is_array($response['messages']));
    }
}
