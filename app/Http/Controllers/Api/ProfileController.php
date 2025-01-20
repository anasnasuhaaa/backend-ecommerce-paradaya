<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    //
    public function storeupdate(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'age' => 'required',
            'bio' => 'required',
        ], [
            'required' => 'input :attribute required'
        ]);

        $profile = Profile::updateOrCreate([
            'user_id' => $user->id
        ], [
            'age' => $request->input('age'),
            'bio' => $request->input('bio'),
        ]);

        return response([
            'message' => 'Profile berhasil dibuat/diupdate',
            'data' => $profile
        ], 200);
    }
}
