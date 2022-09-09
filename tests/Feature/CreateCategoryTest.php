<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCategoryTest extends TestCase
{
    use RefreshDatabase;

    public function testCreateCategoryOk()
    {
        $category = Category::factory()->make();
        $response = $this->post('/api/v1/category', $category->toArray());

        $response->assertCreated();
        $this->assertDatabaseHas('categories', $category->toArray());
    }
}