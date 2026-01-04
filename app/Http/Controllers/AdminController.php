<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\BaseProduct;
use App\Models\SizeColor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);

        $admin = Admin::query()->create($validatedData);
        $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;

        return response()->json([
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $admin = Admin::query()->where('email', $validatedData['email'])->first();
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return response()->json(["message" => "Invalid Credentials"]);
        }
        $token = $admin->createToken('admin_token', ['admin'])->plainTextToken;
        return response()->json(["admin" => $admin, "token" => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(["message" => "Logged out"]);
    }

    //------------------------------
    // Shop Controller
    //---------------------------

    public function shopProducts(Request $request){
        return response()->json([
            "products" => BaseProduct::with('sizeColors')->get(),
        ]);
    }
    public function addProductBase(Request $request){
        $validated = $request->validate(['name' => 'required|string|max:255']);
        BaseProduct::query()->create($validated);
        return response()->json(['message' => 'Product added successfully']);
    }
    public function editProducts(Request $request, int $id){
        $product = BaseProduct::query()->findOrFail($id);
        $validated = $request->validate(['name' => 'required|string|max:255']);
        $product->update($validated);

        return response()->json(['message' => 'Product updated successfully']);
    }
    public function deleteProduct(Request $request, int $id){
        BaseProduct::query()->findOrFail($id)->delete();
        return response()->json(['message' => 'Product removed successfully']);
    }
    public function addImageToProduct(Request $request, $id){
        $product = BaseProduct::query()->findOrFail($id);
        $request->validate([
            'images' => ['required'],['array'],
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);
        $images = $product->images ?? [];
        foreach ($request->file('images') as $image)
            $images[] = $image->store('products', 'public');
        $product->update(['images' => $images]);

        return response()->json(['message' => 'Images added successfully']);
    }

    public function addProductBranch(Request $request, int $productId){
        $validated = $request->validate([
            'size'     => 'required|string|max:50',
            'color'    => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);
        $product = BaseProduct::query()->findOrFail($productId)->sizeColors()->create($validated);

        return response()->json(['message' => 'Product branch added successfully']);
    }
    public function editProductSizeColor(Request $request, int $productSizeColorId){
        $validated = $request->validate([
            'size'     => 'required|string|max:50',
            'color'    => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);
        $product = SizeColor::query()->findOrFail($productSizeColorId)->update($validated);
        return response()->json(['message' => 'Product size color updated successfully']);
    }
    public function deleteProductSizeColor(Request $request, int $id){
        sizeColor::query()->findOrFail($id)->delete();
        return response()->json(['message' => 'Product size and color removed successfully']);
    }
}
//$branch = SizeColor::query()->
//whereHas('product', function ($q) use ($request) {
//    $q->where('user_id', $request->user()->id);
//})->findOrFail($id);

