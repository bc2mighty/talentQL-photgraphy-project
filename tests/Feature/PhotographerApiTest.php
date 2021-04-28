<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhotographerApiTest extends TestCase
{
    /** @test */
    public function create_photographer()
    {
        $testData = [
            "name" => "Shade Shegun",
            "brand" => "Shegun Visuals",
            "phone" => "09137299300",
            "address" => "Mabushi Abuja",
            "email" => "shadesegun4@gmail.com",
            "password" => "mighty"
        ];

        $response = $this->json("POST", '/api/photographer/create', $testData);

        $response
            ->assertStatus(201);
    }
}
