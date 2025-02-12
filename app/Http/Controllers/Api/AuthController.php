<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\OtpCode;
use Illuminate\Http\Request;
use App\Mail\GenerateOtpMail;
use App\Mail\UserRegisterMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $user = User::with('role')->where('email', $request->input('email'))->first();

        // return $this->respondWithToken($token);
        return response([
            'message' => 'Berhasil Lgoin',
            'user' => $user,
            'token' => $token,
        ], 200);
    }
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,id',
            'password' => 'required|min:8|confirmed'
        ]);

        $user = new User();

        $roleUser = Role::where('name', 'user')->value('id');
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role_id = $roleUser;
        $user->save();

        Mail::to($user->email)->send(new UserRegisterMail($user));

        return response([
            'message' => 'User berhasil register, silakan cek email',
            'user' => $user
        ], 201);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Berhasil Logout']);
    }
    public function me()
    {
        $user = auth()->user();
        return response([
            'message' => 'Berhasil menampilkan data user saat ini',
            'user' => $user
        ], 200);
    }
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'sometimes|min:2',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:8|confirmed'
        ]);

        $updated = false;

        if ($request->has('name')) {
            $user->name = $request->input('name');
            $updated = true;
        }

        if ($request->has('email')) {
            $user->email = $request->input('email');
            $updated = true;
        }

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
            $updated = true;
        }

        if ($updated) {
            $user->save();
            return response([
                'message' => 'User berhasil diperbarui',
                'user' => $user
            ], 200);
        }

        return response([
            'message' => 'Tidak ada yang diperbarui'
        ], 200);
    }
    public function generateOtpCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $user = User::where('email', $request->input('email'))->first();

        $user->generate_otp();

        Mail::to($user->email)->send(new GenerateOtpMail($user));
        return response()->json([
            'message' => 'OTP berhasil dikirim, silakan chek email anda',
        ], 200);
    }
    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required|min:6',
        ]);
        $user = auth()->user();
        $otp_code = OtpCode::where('otp', $request->input('otp'))->first();
        if (!$otp_code) {
            return response([
                'message' => 'OTP code tidak ditemukan'
            ], 404);
        }
        // Check exp OTP If Now > Valid Until
        $now = Carbon::now();
        if ($now > $otp_code->valid_until) {
            return response([
                "message" => "OTP code sudah tidak berlaku, silakan generate ulang OTP"
            ], 400);
        }
        // Update email virified user
        $user->email_verified_at = Carbon::now();
        $user->save();
        $otp_code->delete();
        return response([
            "message" => "Verifikasi email berhasil"
        ], 200);
    }
}
