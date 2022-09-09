<?php

namespace Tests\Feature;

use App\Models\Category;
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
            'id'           => $productOld->id,
            'name'         => $productNew->name,
            'price'        => $productNew->price,
            'is_published' => $productNew->is_published,
        ]);
    }

    public function testUpdateProductWithCategories()
    {
        $productOld = Product::factory()->create();
        $productNew = Product::factory()->make();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        $response = $this->patch("/api/v1/product/{$productOld->id}",
            array_merge($productNew->toArray(), ['categories' => [$category1->id, $category2->id]]));

        $response->assertOk();
        $this->assertDatabaseHas('products', [
            'id'           => $productOld->id,
            'name'         => $productNew->name,
            'price'        => $productNew->price,
            'is_published' => $productNew->is_published,
        ]);
        $this->assertDatabaseHas('category_product',
            ['product_id' => $productOld->id, 'category_id' => $category1->id]);
        $this->assertDatabaseHas('category_product',
            ['product_id' => $productOld->id, 'category_id' => $category2->id]);
    }
}