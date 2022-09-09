<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        $filters = $request->get('filter');
        if (!empty($filters['name'])) {
            $query->where(['name' => $filters['name']]);
        }
        if (!empty($filters['category_id'])) {
            $query->whereHas('categories', function (Builder $query) use ($filters) {
                $query->where('categories.id', '=', $filters['category_id']);
            });
        }
        if (!empty($filters['category_name'])) {
            $query->whereHas('categories', function (Builder $query) use ($filters) {
                $query->where('categories.name', '=', $filters['category_name']);
            });
        }
        if (!empty($filters['price_from'])) {
            $query->where('price', '>=', $filters['price_from']);
        }
        if (!empty($filters['price_to'])) {
            $query->where('price', '<=', $filters['price_to']);
        }
        if (isset($filters['is_published'])) {
            $query->where(['is_published' => $filters['is_published']]);
        }
        if (!empty($filters['with_trashed'])) {
            $query->withTrashed();
        }

        $products = $query->get();

        return ProductCollection::make($products);
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        if (!empty($request->categories)) {
            $product->categories()->sync($request->categories);
        }
        return ProductResource::make($product);
    }

    public function show(Product $product)
    {
        return ProductResource::make($product);
    }

    public function update(ProductRequest $request, Product $product)
    {
        if ($product->update($request->validated())) {
            if (!empty($request->categories)) {
                $product->categories()->sync($request->categories);
            }
            return ProductResource::make($product);
        }

        return response(['error' => 'Product update failed'], Response::HTTP_BAD_REQUEST);
    }

    public function destroy(Product $product)
    {
        return $product->delete() ? response([], Response::HTTP_OK) : response(['error' => 'Product deletion failed'],
            Response::HTTP_BAD_REQUEST);
    }
}