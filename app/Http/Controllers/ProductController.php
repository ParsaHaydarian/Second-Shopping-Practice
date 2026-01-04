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


    /*
      PRODUCT BRANCHES
     */


    public function update_product_branch(Request $request, int $id)
    {
        $validated = $request->validate([
            'size'     => 'required|string|max:50',
            'color'    => 'required|string|max:50',
            'price'    => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
        ]);

        $branch = SizeColor::query()->whereHas('product', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $branch->update($validated);

        return response()->json([
            'message' => 'Product branch updated successfully',
        ]);
    }

    public function delete_product_branch(Request $request, int $id)
    {
        $branch = SizeColor::query()->
        whereHas('product', function ($q) use ($request) {
            $q->where('user_id', $request->user()->id);
        })->findOrFail($id);

        $branch->delete();

        return response()->json([
            'message' => 'Product branch removed successfully',
        ]);
    }

    /*
      ORDERS
    */










}
