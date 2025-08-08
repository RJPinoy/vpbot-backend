<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class LoginTest extends ApiTestCase
{
    public function testSomething(): void
    {
        $response = static::createClient()->request('POST', '/api/login', ['json'=> [
            "email" => "superadmin@extranet.com",
            "password" => "superadmin123",
        ]]);

        $this->assertResponseIsSuccessful();
        // $this->assertJsonContains(['@id' => '/']);
    }
}
