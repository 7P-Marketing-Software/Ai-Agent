<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Traits\ArchiveTrait;

class User extends Authenticatable
{
    use HasFactory,HasApiTokens, ArchiveTrait,SoftDeletes;

    protected $guard = 'user';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'address',
        'gender',
        'otp',
        'otp_sent_at',
        'otp_verified_at',
        'otp_expires_at',
        'otp_attempts',
        'points',
        'remember_token',
        'banned_until'
    ];

    protected $hidden = [
        'password',
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class);
    }
}