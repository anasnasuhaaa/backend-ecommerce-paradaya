<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $data = Role::all();
        return response([
            'message' => 'Tampil Data Berhasil',
            'data' => $data
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required',
        ], [
            'required' => 'input :attribute required'
        ]);


        $newCast = Role::create([
            'name' => $request->input('name'),
            'created_at' => Carbon::now()
        ]);

        return response([
            'message' => 'Tambah Role Berhasil',
            'name' => $newCast->name,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
        $data = Role::find($id);
        if (!$data) {
            return response([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return response([
            'message' => 'Detail Data Role',
            'name' => $data->name,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
        $data = Role::find($id);
        if (!$data) {
            return response([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        $data->update([
            'name' => $request->input('name'),
            'updated_at' => Carbon::now()
        ]);
        return response([
            'message' => 'Update Role Berhasil',
            'name' => $data->name,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $data = Role::find($id);
        if (!$data) {
            return response([
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        $data->delete();
        return response([
            'message' => 'Berhasil menghapus Role'
        ], 200);
    }
}
