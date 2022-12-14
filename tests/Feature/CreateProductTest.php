<?php

namespace Tests\Feature;

use App\Models\Category;
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

    public function testCreateProductWithCategories()
    {
        $product = Product::factory()->make();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $response = $this->post('/api/v1/product',
            array_merge($product->toArray(), ['categories' => [$category1->id, $category2->id]]));

        $id = $response->json('data.id');

        $response->assertCreated();
        $this->assertDatabaseHas('products', $product->toArray());
        $this->assertDatabaseHas('category_product', ['product_id' => $id, 'category_id' => $category1->id]);
        $this->assertDatabaseHas('category_product', ['product_id' => $id, 'category_id' => $category2->id]);
    }
}