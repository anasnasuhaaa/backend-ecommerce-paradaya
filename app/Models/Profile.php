<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory, HasUuids;
    protected $table = 'profiles';
    protected $fillable = [
        'bio',
        'age',
        'user_id'
    ];
    public function users()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
