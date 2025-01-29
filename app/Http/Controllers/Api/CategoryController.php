<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\CafeOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = new Category();
        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        }

        $category->save();

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category, 200);
    }

    public function getProductsByCategory($categoryId, $ownerId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Get all products linked to this category
        $allProducts = $category->products;

        // Get assigned products only for this owner
        $assignedProducts = Product::whereHas('cafeOwners', function ($query) use ($categoryId, $ownerId) {
            $query->where('category_id', $categoryId)
                  ->where('cafe_owner_id', $ownerId); // Ensure only the current owner's products are fetched
        })->with(['cafeOwners' => function ($query) use ($categoryId, $ownerId) {
            $query->where('category_id', $categoryId)
                  ->where('cafe_owner_id', $ownerId);
        }])->get();

        // Include the correct pivot price for the owner
        $assignedProducts->each(function ($product) use ($ownerId) {
            $product->owner_price = optional($product->cafeOwners->first())->pivot->price ?? null;
        });

        // Get unassigned products (products in category but not assigned to the current owner)
        $assignedProductIds = $assignedProducts->pluck('id')->toArray();
        $unassignedProducts = $allProducts->whereNotIn('id', $assignedProductIds)->values();

        return response()->json([
            'assignedProducts' => $assignedProducts,
            'unassignedProducts' => $unassignedProducts,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'image' => 'nullable', // Allow the image field to be nullable
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category->name = $request->name;
        $category->slug = Str::slug($request->name);

        // Check if the image field is a file (new upload) or a string (existing image path)
        if ($request->hasFile('image')) {
            // If a new image is uploaded, store it and update the image path
            $imagePath = $request->file('image')->store('categories', 'public');
            $category->image = $imagePath;
        } elseif ($request->image && is_string($request->image)) {
            // If the image field is a string (existing image path), keep the existing image
            $category->image = $request->image;
        } else {
            // If no image is provided, set the image to null (or keep the existing image)
            $category->image = $category->image; // Keep the existing image
        }

        $category->save();

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }

    public function showCategory($username, $categorySlug)
    {
        // Find the cafe owner
        $owner = CafeOwner::where('username', $username)->firstOrFail();

        // Find the category
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        // Fetch products assigned to this owner and category from cafe_owner_product
        $products = Product::whereHas('cafeOwners', function ($query) use ($owner, $category) {
            $query->where('cafe_owner_id', $owner->id)
                ->where('category_id', $category->id);
        })->with(['cafeOwners' => function ($query) use ($owner, $category) {
            $query->where('cafe_owner_id', $owner->id)
                ->where('category_id', $category->id);
        }])->get();

        // Attach price from pivot table
        foreach ($products as $product) {
            $product->owner_price = optional($product->cafeOwners->first())->pivot->price ?? 'N/A';
        }

        return view('category.show', compact('owner', 'category', 'products'));
    }
}
