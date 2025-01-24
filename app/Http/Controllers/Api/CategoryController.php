<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
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
        $categories = Category::get();

        return response([
            'message' => 'Tampil Kategori Berhasil',
            'data' => $categories,
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ], [
            'required' => "Input :attribute field is required.",
        ]);

        $categories = new Category();

        $categories->name = $request->input('name');

        $categories->save();

        return response([
            'message' => "Tambah kategori Berhasil",
            'data' => $categories
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $categories = Category::with(['listProducts'])->find($id);

        if (!$categories) {
            return response([
                'message' => "Detail Kategori Tidak Ditemukan",
            ], 404);
        }

        return response([
            'message' => "Detail Kategori",
            'data' => $categories,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|max:255',

        ], [
            'required' => "Input :attribute field is required.",
        ]);

        $categories = Category::find($id);

        if (!$categories) {
            return response([
                'message' => "Detail Kategori Tidak Ditemukan",
            ], 404);
        }

        $categories->name = $request->input('name');

        $categories->save();

        return response([
            'message' => "Update kategori Berhasil",
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $categories = Category::find($id);

        if (!$categories) {
            return response([
                'message' => "Kategori Tidak Ditemukan",
            ], 404);
        }

        $categories->delete();

        return response([
            'message' => "Delete Kategori Berhasil",
        ], 200);
    }
}
