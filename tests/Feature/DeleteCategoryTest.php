<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testDeleteCategoryOk()
    {
        $category = Category::factory()->create();
        $response = $this->delete("/api/v1/category/{$category->id}");

        $response->assertOk();
        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    public function testDeleteCategoryMissing()
    {
        $category = Category::factory()->create();
        $response = $this->delete("/api/v1/category/999");

        $response->assertNotFound();
        $this->assertNotSoftDeleted('categories', ['id' => $category->id]);
    }

    public function testDeleteCategoryWithProducts()
    {
        $category = Category::factory()->has(Product::factory()->count(5))->create();
        $response = $this->delete("/api/v1/category/{$category->id}");

        $response->assertUnprocessable();
        $this->assertNotSoftDeleted('categories', ['id' => $category->id]);
    }
}