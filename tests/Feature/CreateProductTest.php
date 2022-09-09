<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateProductOk()
    {
        $product = Product::factory()->make();
        $response = $this->post('/api/v1/product', $product->toArray());

        $response->assertCreated();
        $this->assertDatabaseHas('products', $product->toArray());
    }

    public function testCreateProductMissingName()
    {
        $product = Product::factory()->make(['name' => null]);
        $response = $this->post('/api/v1/product', $product->toArray());

        $response->assertInvalid();
        $this->assertDatabaseMissing('products', $product->toArray());
    }
}