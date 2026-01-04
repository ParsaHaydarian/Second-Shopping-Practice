<?php

namespace App\Http\Controllers;

use App\Models\BaseProduct;
use App\Models\SizeColor;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Sell;

class ProductController extends Controller
{
    /*

      PRODUCTS

    */

    public function add_product_model(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user->products()->create($validated);

        return response()->json([
            'message' => 'Product added successfully',
        ], 201);
    }

    public function edit_product(Request $request, int $id)
    {
        $user = $request->user();

        $product = $user->products()->findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
        ]);
    }

    public function remove_product(Request $request, int $id)
    {
        $user = $request->user();

        $product = $user->products()->findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Product removed successfully',
        ]);
    }

    public function get_my_products(Request $request)
    {
        $products = $request->user()
            ->products()
            ->with('sizeColors')
            ->get();

        return response()->json([
            'products' => $products,
        ]);
    }

    public function get_all_products()
    {
        return response()->json([
            'products' => BaseProduct::with('sizeColors')->get(),
        ]);
    }

    /*
      PRODUCT BRANCHES
     */

    public function add_product_branch(Request $request, int $productId)
    {
        $user = $request->user();

        $validated = $request->validate([
            'size'     => 'required|string|max:50',
            'color'    => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $product = $user->products()->findOrFail($productId);

        $product->sizeColors()->create($validated);

        return response()->json([
            'message' => 'Product branch added successfully',
        ], 201);
    }

    public function update_product_branch(Request $request, int $id)
    {
        $validated = $request->validate([
            'size'     => 'required|string|max:50',
            'color'    => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $branch = SizeColor::whereHas('product', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $branch->update($validated);

        return response()->json([
            'message' => 'Product branch updated successfully',
        ]);
    }

    public function delete_product_branch(Request $request, int $id)
    {
        $branch = SizeColor::whereHas('product', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        //i should add user_id to product branch but i didnt so i cant use this =>
        //$branch = SizeColor::query()->where('user_id' , $user->id)->findOrFail($id)

        $branch->delete();

        return response()->json([
            'message' => 'Product branch removed successfully',
        ]);
    }

    /*
      ORDERS
    */

    public function add_order(Request $request, int $branchId)
    {
        $user = $request->user();

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $branch = SizeColor::findOrFail($branchId);

        if ($validated['quantity'] > $branch->quantity) {
            return response()->json([
                'message' => 'Requested quantity exceeds available stock',
            ]);
        }

        $order = $user->orders()->create([
            'product_id' => $branch->id,
            'quantity'   => $validated['quantity'],
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'order'   => $order,
        ], 201);
    }

    public function edit_order(Request $request, int $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $order = Order::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $order->update($validated);

        return response()->json([
            'message' => 'Order updated successfully',
        ]);
    }

    public function remove_order(Request $request, int $id)
    {
        $order = Order::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $order->delete();

        return response()->json([
            'message' => 'Order removed successfully',
        ]);
    }

    public function get_orders(Request $request)
    {
        return response()->json([
            'orders' => $request->user()
                ->orders()
                ->get(),
        ]);
    }

    public function buy_order(Request $request)
    {

        $user = $request->user();
        foreach ($user->orders as $order) {
            $product = SizeColor::findOrFail($order->product_id);
            $product->quantity -= $order->quantity;
            $product->save();
        }

        Sell::create([
            'user_id' => $user->id,
            'orders'  => $user->orders()->get()->toJson(),
        ]);

        $user->orders()->delete();

        return response()->json([
            'message' => 'Order placed successfully',
        ]);
    }

    public function add_image(Request $request, $id)
    {
        $product = BaseProduct::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $images = $product->images ?? [];

        foreach ($request->file('images') as $image) {
            $path = $image->store('products', 'public');
            $images[] = $path;
        }

        $product->update([
            'images' => $images,
        ]);

        return response()->json([
            'message' => 'Images appended successfully',
            'images' => $images,
        ]);
    }
}
