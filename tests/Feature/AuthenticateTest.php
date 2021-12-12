<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticateTest extends TestCase
{
    use WithFaker;

    
    public function testRequiredFieldsForRegistration()
    {
        $response = $this->json('POST', 'api/auth/register', ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJson([
                "message" => [
                    "The name field is required.",
                    "The email field is required.",
                    "The password field is required."
                ]
            ]);
    }



    public function testRequiredFieldsForLogin()
    {
        $response = $this->json('POST', 'api/auth/login', ['Accept' => 'application/json']);

        $response->assertStatus(422);
        $response->assertJson([
                "message" => [
                    "The email field is required.",
                    "The password field is required."
                ]
            ]);
    }



    public function testUserIsCreatedSuccessfully() {
    
        $payload = [
            'name' => $this->faker->name,
            'email'  => $this->faker->email,
            'password' => '123456',
            'password_confirmation' => '123456'
        ];

        $this->json('post', 'api/auth/register', $payload)
             ->assertStatus(201)
             ->assertCreated()
             ->assertJsonStructure(
                [
                    "message",
                    "user" => [
                        "name",
                        "email",
                        "created_at",
                        "updated_at"
                    ],
                    "token"
                ]
             );
    }


    public function testUserLoginSuccessfully() {
    
        $data = [
            'email'  => 'parspack@gmail.com',
            'password' => '123456'
        ];

        $this->json('post', 'api/auth/login', $data)
             ->assertStatus(200)
             ->assertJsonStructure(
                [
                    "message",
                    "user" => [
                        "name",
                        "email",
                        "created_at",
                        "updated_at"
                    ],
                    "token"
                ]
        );
    
    }
}
