<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sell;
use App\Models\SizeColor;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function addOrder(Request $request, int $branchId)
    {
        $validated = $request->validate(['quantity' => 'required|integer|min:1',]);
        $user = $request->user();
        $branch = SizeColor::query()->findOrFail($branchId);

        if ($validated['quantity'] > $branch->quantity)
            return response()->json(['message' => 'Requested quantity exceeds available stock',]);

        $order = $user->orders()->create([
            'product_id' => $branch->id,
            'quantity'   => $validated['quantity'],
        ]);

        return response()->json(['message' => 'Order placed successfully',]);
    }
    public function editOrder(Request $request, int $id){
        $validated = $request->validate(['quantity' => 'required|integer|min:1',]);
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id)
            ->update($validated);
        return response()->json(['message' => 'Order updated successfully',]);
    }
    public function deleteOrder(Request $request, int $id){
        $order = Order::query()
            ->where('user_id', $request->user()->id)
            ->findOrFail($id)
            ->delete();

        return response()->json(['message' => 'Order removed successfully',]);
    }
    public function getOrder(Request $request){
        return response()->json([
            'orders' =>
                $request
                    ->user()
                    ->orders()
                    ->get(),
        ]);
    }

    public function buyOrders(Request $request){
        $user = $request->user();
        $totalPrice = 0;
        foreach ($user->orders as $order) {
            $product = SizeColor::query()->findOrFail($order->product_id);
            $product->quantity -= $order->quantity;

            $totalPrice = $product->price * $order->quantity;

            $product->save();
        }

        Sell::query()->create([
                'user_id' => $user->id,
                'orders'  => $user->orders()->get()->toJson(),
                //'total_price' => $totalPrice,
            ]);

        $user->orders()->delete();
        return response()->json([
            'message' => 'Order placed successfully',
            "total_price" =>$totalPrice
        ]);
    }
}
