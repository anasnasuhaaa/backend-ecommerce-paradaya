<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Orders;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Products;

class OrderController extends Controller
{
    /**
     * List all orders (Admin)
     */
    public function index()
    {
        $orders = Orders::with(['user', 'product'])->get();
        return response()->json($orders, 200);
    }

    /**
     * Show single order detail (Admin)
     */
    public function show($id)
    {
        $order = Orders::with(['user', 'product'])->find($id);
        if(!$order){
            return response()->json(['message' => 'Order not found.'], 404);
        }
        return response()->json($order, 200);
    }

    /**
     * Create a new order (Verified Users)
     */
    public function store(Request $request)
{
    // Validasi input
    $validator = Validator::make($request->all(), [
        'product_id' => 'required|uuid|exists:products,id',
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'address' => 'required|string',
        'quantity' => 'required|integer|min:1',
    ]);

    if($validator->fails()){
        return response()->json($validator->errors(), 422);
    }

    // Mendapatkan pengguna yang terautentikasi
    $user = JWTAuth::parseToken()->authenticate();

    // Mendapatkan produk secara langsung dari model Product
    $product = Products::find($request->product_id);
    if(!$product){
        return response()->json(['message' => 'Product not found.'], 404);
    }

    // Memeriksa stok produk
    if($product->stock < $request->quantity){
        return response()->json(['message' => 'Insufficient stock.'], 400);
    }

    // Menghitung total harga
    $totalPrice = $product->price * $request->quantity;

    // Mengurangi stok produk
    $product->stock -= $request->quantity;
    $product->save();

    // Membuat pesanan
    $order = Orders::create([
        'product_id' => $request->product_id,
        'user_id' => $user->id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'address' => $request->address,
        'quantity' => $request->quantity,
        'total_price' => $totalPrice,
        'status' => 'pending',
    ]);

    return response()->json(['message' => 'Order created successfully.', 'order' => $order], 201);
}

    /**
     * Update order status (Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Orders::find($id);
        if(!$order){
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,success,cancel',
        ]);

        if($validator->fails()){
            return response()->json($validator->errors(), 422);
        }

        // Update status
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order], 200);
    }

    /**
     * Delete an order (Admin)
     */
    public function destroy($id)
    {
        $order = Orders::find($id);
        if(!$order){
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.'], 200);
    }
}