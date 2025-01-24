<?php

namespace App\Http\Controllers\Api;

use App\Models\Orders;
use App\Models\Products;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Egulias\EmailValidator\Result\Reason\Reason;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Orders::with(['user', 'product'])->get();
        return response()->json($orders, 200);
    }

    /**
     * Store a newly created resource in storage.
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

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Mendapatkan pengguna yang terautentikasi
        $user = auth()->user();

        // Mendapatkan produk secara langsung dari model Product
        $product = Products::find($request->product_id);
        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Memeriksa stok produk
        if ($product->stock < $request->quantity) {
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
            'order_id' => uniqid(),
            'user_id' => $user->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address' => $request->address,
            'quantity' => $request->quantity,
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);


        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;

        $params = array(
            'transaction_details' => array(
                'order_id' => rand(),
                'gross_amount' =>  $totalPrice
            ),
            'customer_details' => array(
                'first_name' => $order->first_name,
                'last_name' => $order->last_name,
                'email' => $user->email,
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        // return response()->json(['token' => $snapToken]);

        return response()->json(['message' => 'Order created successfully.', 'order' => $order, "snap_token" => $snapToken], 201);
    }
    // public function store(Request $request)
    // {
    //     // Validasi input
    //     $validator = Validator::make($request->all(), [
    //         'product_id' => 'required|uuid|exists:products,id',
    //         'first_name' => 'required|string|max:255',
    //         'last_name' => 'required|string|max:255',
    //         'email' => 'required|email',
    //         'address' => 'required|string',
    //         'quantity' => 'required|integer|min:1',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 422);
    //     }

    //     // Mendapatkan produk
    //     $product = Products::find($request->product_id);
    //     if (!$product) {
    //         return response()->json(['message' => 'Product not found.'], 404);
    //     }

    //     // Memeriksa stok produk
    //     if ($product->stock < $request->quantity) {
    //         return response()->json(['message' => 'Insufficient stock.'], 400);
    //     }

    //     // Menghitung total harga
    //     $totalPrice = $product->price * $request->quantity;

    //     // Mengurangi stok produk
    //     $product->stock -= $request->quantity;
    //     $product->save();

    //     // Membuat pesanan
    //     $order = Orders::create([
    //         'product_id' => $product->id,
    //         'user_id' => auth()->user()->id, // Mendapatkan ID pengguna yang terautentikasi
    //         'first_name' => $request->first_name,
    //         'last_name' => $request->last_name,
    //         'email' => $request->email,
    //         'address' => $request->address,
    //         'quantity' => $request->quantity,
    //         'total_price' => $totalPrice,
    //         'status' => 'pending',
    //     ]);

    //     // Konfigurasi Midtrans
    //     \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
    //     \Midtrans\Config::$isProduction = false;
    //     \Midtrans\Config::$isSanitized = true;
    //     \Midtrans\Config::$is3ds = true;

    //     // Menyiapkan data transaksi untuk Midtrans
    //     $params = [
    //         'transaction_details' => [
    //             'order_id' => $order->id,
    //             'gross_amount' => $totalPrice,
    //         ],
    //         'customer_details' => [
    //             'first_name' => $request->first_name,
    //             'last_name' => $request->last_name,
    //             'email' => $request->email,
    //             'phone' => $request->phone,
    //         ],
    //         'item_details' => [
    //             [
    //                 'id' => $product->id,
    //                 'price' => $product->price,
    //                 'quantity' => $request->quantity,
    //                 'name' => $product->name,
    //             ],
    //         ],
    //     ];

    //     // Mendapatkan token Snap Midtrans
    //     $snapToken = \Midtrans\Snap::getSnapToken($params);

    //     return response()->json([
    //         'message' => 'Order created successfully.',
    //         'order' => $order,
    //         'token' => $snapToken,
    //     ]);
    // }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $order = Orders::with(['user', 'product'])->find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }
        return response()->json($order, 200);
    }
    public function update(Request $request, $id)
    {
        $order = Orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Validation
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,success,cancel',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update status
        $order->status = $request->status;
        $order->save();

        return response()->json(['message' => 'Order status updated successfully.', 'order' => $order], 200);
    }

    /**
     * Update the specified resource in storage.
     */

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $order = Orders::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Order deleted successfully.'], 200);
    }
}
