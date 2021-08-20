<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{  
    
    public function test_it_requires_an_email()
    {
        $this->json('POST', 'api/auth/login')
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_a_password()
    {
        $this->json('POST', 'api/auth/login')
            ->assertJsonValidationErrors(['password']);
    }

    public function test_it_returns_a_validation_error_if_credentials_dont_match()
    {
        $user = User::factory()->create();

        $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'N2@opeqw'
        ])
            // ->assertJsonValidationErrors(['errors']);
            ->assertJsonStructure([
                'errors'
            ]);
    }

    public function test_it_returns_a_token_if_credentials_do_match()
    {
        $user = User::factory()->create([
            'password' => bcrypt('D3p0rtiv0@')
        ]);

        $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'D3p0rtiv0@'
        ])
            ->assertJsonStructure([
                'data' => [
                    'token'
                ]
            ]);
    }

    public function test_it_returns_a_user_if_credentials_do_match()
    {
        $user = User::factory()->create([
            'password' => bcrypt('D3p0rtiv0@')
        ]);

        $this->json('POST', 'api/auth/login', [
            'email' => $user->email,
            'password' => 'D3p0rtiv0@'
        ])
            ->assertJsonFragment([
                'email' => $user->email
            ]);
    }

    public function test_it_revoke_the_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('D3p0rtiv0@')
        ]);

        $this->jsonAs($user, 'POST', 'api/auth/logout')
            ->assertJsonFragment([
                'message' => 'Tokens Revoked'
            ]);
    }
}

