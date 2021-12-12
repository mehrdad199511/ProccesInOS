<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class ApiOperationsTest extends TestCase
{
    use WithFaker;
    
    public function setUp(): void
    {
        parent::setUp();
      
    }

    /**
     * @test
     */
    public function receiveServerProcessesSuccefully()
    {
        $user = User::factory()->create([
            'name' => $this->faker->name,
            'email'  => $this->faker->email,
            'password' => bcrypt('123456')
        ]);

        $response = $this->actingAs($user, 'api')
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' .$user->createToken('UserToken')->plainTextToken
            ])->json(
                'GET',
                '/api/operation/getProcesses', 
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
            "result" =>  [
            ]
        ]);
    }


    /**
     * @test
     */
    public function createDirectorySuccefully()
    {
        $user = User::factory()->create([
            'name' => $this->faker->name,
            'email'  => $this->faker->email,
            'password' => bcrypt('123456')
        ]);

        $payload = [
            'title' => 'test'.rand(1, 100000),
        ];

        $response = $this->actingAs($user, 'api')
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' .$user->createToken('UserToken')->plainTextToken
            ])->json(
                'POST',
                '/api/operation/createDirectory', 
                $payload
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
        ]);

    }


    /**
     * @test
     */
    public function createFileSuccefully()
    {
        $user = User::factory()->create([
            'name' => $this->faker->name,
            'email'  => $this->faker->email,
            'password' => bcrypt('123456')
        ]);

        $payload = [
            'title' => 'test'.rand(1, 1000),
            'extension' => 'txt'
        ];

        $response = $this->actingAs($user, 'api')
            ->withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer ' .$user->createToken('UserToken')->plainTextToken
            ])->json(
                'POST',
                '/api/operation/createDirectory', 
                $payload
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "message",
        ]);

    }


}