<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductOwnerApiTest extends TestCase
{
    /** @test */
    public function create_product_owner()
    {
        $testData = [
            "company_name" => "Apex Waters",
            "slack_hook_url" => "https://hooks.slack.com/services/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",
            "email" => "apexwaters@gmail.com",
            "password" => "mighty"
        ];

        $response = $this->json("POST", '/api/productOwner/create', $testData);

        $response
            ->assertStatus(201);
    }
}
