<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    public function jsonAs($user, $method, $endpoint, $data = [], $headers = [])
    {
        $token = $user->createToken('API Token')->plainTextToken;

        return $this->json($method, $endpoint, $data, array_merge($headers, [
            'Authorization' => 'Bearer ' . $token
        ]));
    }
}
