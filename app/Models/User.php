<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Profile;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable, HasUuids;
    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id'
    ];
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    public static function boot()
    {
        parent::boot();
        static::created(function ($model) {
            $model->generate_otp();
        });
    }
    public function generate_otp()
    {
        do {
            $random_number = mt_rand(100000, 999999);
            $check = otpCode::where('otp', $random_number)->first();
        } while ($check);

        $now = Carbon::now();

        $otp_code = otpCode::updateOrCreate([
            'user_id' => $this->id
        ], [
            'otp' => $random_number,
            'valid_until' => $now->addMinutes(5)
        ]);
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id');
    }
    public function otpData()
    {
        return $this->hasOne(otpCode::class, 'user_id');
    }
}
