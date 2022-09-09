<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductListTest extends TestCase
{
    use RefreshDatabase;

    public function testProductListWithoutParameters()
    {
        $response = $this->get(route('products.list', [], false));

        $response->assertOk();
    }

    public function testProductListFilterName()
    {
        $nameForFilter = fake()->word;
        $nameNotForFilter = fake()->word;
        $nameForFilterExtended = $nameForFilter . fake()->word;
        $product1 = Product::factory()->create(['name' => $nameForFilter]);
        $product2 = Product::factory()->create(['name' => $nameNotForFilter]);
        $product3 = Product::factory()->create(['name' => $nameForFilterExtended]);
        $response = $this->get(route('products.list', ['filter' => ['name' => $nameForFilter]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonMissing(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonMissing(['id' => $product3->id, 'name' => $product3->name]);
    }

    public function testProductListFilterCategoryId()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();
        $product1->categories()->attach($category1);
        $product2->categories()->attach($category2);
        $product3->categories()->attach($category1);
        $product3->categories()->attach($category2);
        $response = $this->get(route('products.list', ['filter' => ['category_id' => $category1->id]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonMissing(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonFragment(['id' => $product3->id, 'name' => $product3->name]);
    }

    public function testProductListFilterCategoryName()
    {
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product3 = Product::factory()->create();
        $product1->categories()->attach($category1);
        $product2->categories()->attach($category2);
        $product3->categories()->attach($category1);
        $product3->categories()->attach($category2);
        $response = $this->get(route('products.list', ['filter' => ['category_name' => $category1->name]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonMissing(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonFragment(['id' => $product3->id, 'name' => $product3->name]);

        $response->assertOk();
    }

    public function testProductListFilterPrice()
    {
        $product1 = Product::factory()->create(['price' => fake()->randomFloat(2, 10, 50)]);
        $product2 = Product::factory()->create(['price' => fake()->randomFloat(2, 51, 100)]);
        $product3 = Product::factory()->create(['price' => fake()->randomFloat(2, 101, 500)]);
        $response = $this->get(route('products.list', ['filter' => ['price_from' => 10]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonFragment(['id' => $product3->id, 'name' => $product3->name]);

        $response = $this->get(route('products.list', ['filter' => ['price_from' => 51]], false));

        $response->assertOk();
        $response->assertJsonMissing(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonFragment(['id' => $product3->id, 'name' => $product3->name]);

        $response = $this->get(route('products.list', ['filter' => ['price_to' => 50]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonMissing(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonMissing(['id' => $product3->id, 'name' => $product3->name]);

        $response = $this->get(route('products.list', ['filter' => ['price_to' => 100]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonMissing(['id' => $product3->id, 'name' => $product3->name]);

        $response = $this->get(route('products.list', ['filter' => ['price_from' => 51, 'price_to' => 100]], false));

        $response->assertOk();
        $response->assertJsonMissing(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
        $response->assertJsonMissing(['id' => $product3->id, 'name' => $product3->name]);
    }

    public function testProductListFilterPublished()
    {
        $product1 = Product::factory()->create(['is_published' => true]);
        $product2 = Product::factory()->create(['is_published' => false]);

        $response = $this->get(route('products.list', ['filter' => ['is_published' => true]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonMissing(['id' => $product2->id, 'name' => $product2->name]);

        $response = $this->get(route('products.list', ['filter' => ['is_published' => false]], false));

        $response->assertOk();
        $response->assertJsonMissing(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
    }

    public function testProductListFilterDeleted()
    {
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();
        $product2->delete();
        $response = $this->get(route('products.list', ['filter' => ['with_trashed' => true]], false));

        $response->assertOk();
        $response->assertJsonFragment(['id' => $product1->id, 'name' => $product1->name]);
        $response->assertJsonFragment(['id' => $product2->id, 'name' => $product2->name]);
    }
}