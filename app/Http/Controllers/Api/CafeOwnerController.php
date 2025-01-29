<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CafeOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Category;

class CafeOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $owners = CafeOwner::all();
        return response()->json($owners, 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Add validation for password in store method
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:cafe_owners,email',
            'username' => 'required|string|max:255|unique:cafe_owners,username',
            'password' => 'required|string|min:6',  // Add password validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $owner = new CafeOwner();
        $owner->first_name = $request->first_name;
        $owner->last_name = $request->last_name;
        $owner->email = $request->email;
        $owner->username = Str::slug($request->username);
        $owner->password = Hash::make($request->password);  // Hash password before storing
        $owner->save();

        return response()->json(['message' => 'Cafe owner added successfully', 'owner' => $owner], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $owner = CafeOwner::with('categories')->find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $allCategories = Category::all();

        return response()->json([
            'owner' => $owner,
            'categories' => $allCategories
        ], 200);
    }

    public function assignCategories(Request $request, $id)
    {
        $owner = CafeOwner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $validated = $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        // Use attach to add new categories without removing existing ones
        $owner->categories()->attach($validated['category_ids']);

        return response()->json([
            'message' => 'Categories assigned successfully',
            'assigned_categories' => $owner->categories
        ], 200);
    }

    public function unassignCategory($ownerId, $categoryId)
    {
        $owner = CafeOwner::find($ownerId);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        // Detach the category
        $owner->categories()->detach($categoryId);

        return response()->json(['message' => 'Category unassigned successfully'], 200);
    }

    public function assignProducts(Request $request, $ownerId)
    {
        $owner = CafeOwner::find($ownerId);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id',
            'price' => 'required|numeric',
        ]);

        // Attach products with custom price
        foreach ($validated['product_ids'] as $productId) {
            $owner->products()->attach($productId, [
                'category_id' => $validated['category_id'],
                'price' => $validated['price'],
            ]);
        }

        return response()->json(['message' => 'Products assigned successfully'], 200);
    }

    public function unassignProduct($ownerId, $categoryId, $productId)
    {
        $owner = CafeOwner::find($ownerId);

        if (!$owner) {
            return response()->json(['message' => 'Owner not found'], 404);
        }

        // Detach the product
        $owner->products()->wherePivot('category_id', $categoryId)->detach($productId);

        return response()->json(['message' => 'Product unassigned successfully'], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $owner = CafeOwner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Cafe owner not found'], 404);
        }

        // Validate request data
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:cafe_owners,email,' . $owner->id,
            'username' => 'required|string|max:255|unique:cafe_owners,username,' . $owner->id,
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $owner->first_name = $request->first_name;
        $owner->last_name = $request->last_name;
        $owner->email = $request->email;
        $owner->username = Str::slug($request->username);
        $owner->save();

        return response()->json(['message' => 'Cafe owner updated successfully', 'owner' => $owner], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $owner = CafeOwner::find($id);

        if (!$owner) {
            return response()->json(['message' => 'Cafe owner not found'], 404);
        }

        $owner->delete();

        return response()->json(['message' => 'Cafe owner deleted successfully'], 200);
    }

    public function showUsers($username)
    {
        $owner = CafeOwner::where('username', $username)->with('categories')->firstOrFail();
        return view('cafe.show', compact('owner'));
    }

}
