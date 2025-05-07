<?php

namespace Modules\Auth\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use App\Http\Traits\ArchiveTrait;

class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens, ArchiveTrait,SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
    ];

    protected $hidden = [
        'password',
    ];


}
