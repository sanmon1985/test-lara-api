<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateProductOk()
    {
        $productOld = Product::factory()->create();
        $productNew = Product::factory()->make();
        $response = $this->patch("/api/v1/product/{$productOld->id}", $productNew->toArray());

        $response->assertOk();
        $this->assertDatabaseHas('products', [
            'id' => $productOld->id,
            'name' => $productNew->name,
            'price' => $productNew->price,
            'is_published' => $productNew->is_published,
        ]);
    }
}