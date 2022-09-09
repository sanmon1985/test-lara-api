<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteProductTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteProductOk()
    {
        $product = Product::factory()->create();
        $response = $this->delete("/api/v1/product/{$product->id}");

        $response->assertOk();
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    public function testDeleteCategoryMissing()
    {
        $product = Product::factory()->create();
        $response = $this->delete("/api/v1/product/999");

        $response->assertNotFound();
        $this->assertNotSoftDeleted('products', ['id' => $product->id]);
    }
}