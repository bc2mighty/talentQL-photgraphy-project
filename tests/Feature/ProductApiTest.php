<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApiTest extends TestCase
{
    /** @test */
    public function create_product()
    {
        $testData = [
            "title" => "33 Cl Bottle",
            "product_owner_id" => "baf554ae-0969-406b-a45b-a1df954c7613"
        ];

        $response = $this->json("POST", '/api/products', $testData);

        $response
            ->assertStatus(201);
    }
    
    /** @test */
    public function update_product()
    {
        $testData = [
            "email" => "33 Cl Bottle",
            "password" => "mighty"
        ];

        $response = $this->json("PUT", '/api/products/c8c18932-e9bc-42d5-8e2e-2fcb8a3d7c0a', $testData);

        $response
            ->assertStatus(200);
    }
}
