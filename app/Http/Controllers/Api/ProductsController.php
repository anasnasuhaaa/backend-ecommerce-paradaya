<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;

class ProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['isAdmin', 'auth:api'])->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with('category')->get();

        return response([
            'message' => 'Tampil Produk Berhasil',
            'data' => $products,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|integer',
            'description' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:5000',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ], [
            'required' => "Input :attribute field is required.",
            'mimes' => "Input :attribute field is have to be jpeg, png, jpg, gif.",
            'max' => "Input :attribute field is have to max of 5000",
            'image' => "Input :attribute field is have to be image",
            'exists' => "Input :attribute is not found in categories table",
        ]);

        $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
            'folder' => 'images',
        ])->getSecurePath();

        $products = new Products;

        $products->name = $request->input('name');
        $products->price = $request->input('price');
        $products->description = $request->input('description');
        $products->image = $uploadedFileUrl;
        $products->stock = $request->input('stock');
        $products->category_id = $request->input('category_id');

        $products->save();

        return response([
            'message' => "Tambah Produk Berhasil",
            'data' => $products
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $products = Products::with(['category'])->find($id);

        if (!$products) {
            return response([
                'message' => "Detail Produk Tidak Ditemukan",
            ], 404);
        }

        return response([
            'message' => "Detail Produk",
            'data' => $products,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'price' => 'required|integer',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:5000',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
        ], [
            'required' => "Input :attribute field is required.",
            'mimes' => "Input :attribute field is have to be jpeg, png, jpg, gif.",
            'max' => "Input :attribute field is have to max of 5000",
            'image' => "Input :attribute field is have to be image",
            'exists' => "Input :attribute is not found in categories table",
        ]);

        $products = Products::find($id);

        if (!$products) {
            return response([
                'message' => "Detail Produk Tidak Ditemukan",
            ], 404);
        }

        if ($request->hasFile('image')) {
            $uploadedFileUrl = cloudinary()->upload($request->file('image')->getRealPath(), [
                'folder' => 'images',
            ])->getSecurePath();
            $products->image = $uploadedFileUrl;
        }

        $products->name = $request->input('name');
        $products->price = $request->input('price');
        $products->description = $request->input('description');
        $products->stock = $request->input('stock');
        $products->category_id = $request->input('category_id');

        $products->save();

        return response([
            'message' => "Update Produk Berhasil",
            'data' => $products
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $products = Products::find($id);

        if (!$products) {
            return response([
                'message' => "Detail Produk Tidak Ditemukan",
            ], 404);
        }

        $products->delete();

        return response([
            'message' => "Delete Produk Berhasil",
        ], 200);
    }
}
