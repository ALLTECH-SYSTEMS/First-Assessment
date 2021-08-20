<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    
    public function test_it_requires_a_name()
    {
        $this->json('POST', 'api/auth/register')
            ->assertJsonValidationErrors(['name']);
    }

    public function test_it_requires_a_email()
    {
        $this->json('POST', 'api/auth/register')
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_a_valid_email()
    {
        $this->json('POST', 'api/auth/register', [
            'email' => 'nope'
        ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_a_unique_email()
    {
        $user = User::factory()->create();
        // $user = factory(User::class)->create();

        $this->json('POST', 'api/auth/register', [
            'email' => $user->email
        ])
            ->assertJsonValidationErrors(['email']);
    }

    public function test_it_requires_a_password()
    {
        $this->json('POST', 'api/auth/register')
            ->assertJsonValidationErrors(['password']);
    }

    public function test_it_registers_a_user()
    {
        $this->withoutExceptionHandling();

        $this->json('POST', 'api/auth/register', [
            'name' => $name = 'Alex',
            'email' => $email = 'alex@codecourse.com',
            'category' => $category = 0,
            'password' => 'D3p0rtiv0@',
            'password_confirmation' => 'D3p0rtiv0@'
        ]);

        $this->assertDatabaseHas('users', [
            'email' => $email,
            'name' => $name,
            'category' => $category
        ]);
    }


    public function test_it_returns_a_user_on_registration()
    {
        $this->json('POST', 'api/auth/register', [
            'name' => 'Alex',
            'email' => $email = 'alex@codecourse.com',
            'category' => 0,
            'password' => 'D3p0rtiv0@',
            'password_confirmation' => 'D3p0rtiv0@'
        ])
            ->assertJsonFragment([
                'email' => $email
            ]);
    }

}
