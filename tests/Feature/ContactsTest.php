<?php

namespace Tests\Feature;

use Tests\TestCase;

class ContactsTest extends TestCase
{


    public function test_list() {
        $response = $this->get('/api/contacts');

        $response->assertStatus(200)
            ->assertJson([
                'list' => true,
                'totalPages' => true,
                'currentPage' => true,
            ]);

    }

}
