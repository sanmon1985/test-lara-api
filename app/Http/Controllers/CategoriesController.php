<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Response;

class CategoriesController extends Controller
{
    public function index()
    {

    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());
        return CategoryResource::make($category);
    }

    public function show(Category $category)
    {
        return CategoryResource::make($category);
    }

    public function update(CategoryRequest $request, Category $category)
    {

    }

    public function destroy(Category $category)
    {
        if (count($category->products) === 0) {
            return $category->delete() ? response([], Response::HTTP_OK) : response(['error' => 'Category deletion failed'], Response::HTTP_BAD_REQUEST);
        }

        return response(['error' => 'Category is not empty'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}