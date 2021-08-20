<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MeTest extends TestCase
{
    public function test_it_fails_if_user_isnt_authenticated()
    {
        $this->json('GET', 'api/me')
            ->assertStatus(401);
    }

    public function test_it_returns_user_details()
    {
        $user = User::factory()->create();

        $this->jsonAs($user, 'GET', 'api/me')
            ->assertJsonFragment([
                'email' => $user->email
            ]);
    }
}
