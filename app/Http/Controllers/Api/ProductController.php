<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\CafeOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index()
    {
        $products = Product::with('categories')->get();
        return response()->json($products, 200);
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $request->merge([
            'categories' => is_string($request->categories) ? json_decode($request->categories, true) : $request->categories,
        ]);
        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = new Product();
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        // Attach categories to the product
        $product->categories()->attach($request->categories);

        return response()->json(['message' => 'Product created successfully', 'product' => $product], 201);
    }

    /**
     * Display the specified product.
     */
    public function show(string $id)
    {
        $product = Product::with('categories')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product, 200);
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        $request->merge([
            'categories' => is_string($request->categories) ? json_decode($request->categories, true) : $request->categories,
        ]);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Validate incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->description = $request->description;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $product->image = $imagePath;
        }

        $product->save();

        // Sync categories for the product
        $product->categories()->sync($request->categories);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    public function showProduct($username, $categorySlug, $productSlug)
    {
        // Find the cafe owner
        $owner = CafeOwner::where('username', $username)->firstOrFail();

        // Find the category
        $category = Category::where('slug', $categorySlug)->firstOrFail();

        // Find the product and ensure it belongs to this category
        $product = Product::where('slug', $productSlug)
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('categories.id', $category->id);
            })
            ->firstOrFail();

        // Get the price from cafe_owner_product pivot table
        $ownerProduct = $owner->products()
            ->wherePivot('category_id', $category->id)
            ->wherePivot('product_id', $product->id)
            ->first();

        // Attach the price to the product object
        $product->owner_price = $ownerProduct ? $ownerProduct->pivot->price : 'N/A';

        // Find alternative cafes that also sell this product in the same category
        $alternativeCafes = CafeOwner::whereHas('products', function ($query) use ($category, $product) {
            $query->where('cafe_owner_product.category_id', $category->id)
                ->where('cafe_owner_product.product_id', $product->id);
        })
        ->where('id', '!=', $owner->id) // Exclude the current cafe
        ->with(['products' => function ($query) use ($category, $product) {
            $query->where('cafe_owner_product.category_id', $category->id)
                ->where('cafe_owner_product.product_id', $product->id)
                ->select('products.*', 'cafe_owner_product.price'); // Fetch product price from pivot table
        }])
        ->get();

        return view('product.show', compact('owner', 'category', 'product', 'alternativeCafes'));
    }

}