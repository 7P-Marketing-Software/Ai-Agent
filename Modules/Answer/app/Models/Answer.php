<?php

namespace Modules\Answer\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\ArchiveTrait;
use Modules\Auth\Models\User;
use Modules\Category\Models\Category;

class Answer extends Model
{
    use HasFactory, SoftDeletes, ArchiveTrait;
    protected $fillable = [
        'user_id',
        'sub_category_id',
        'report_link',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
