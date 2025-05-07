<?php

namespace Modules\Question\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Http\Traits\ArchiveTrait;
use Modules\AiModel\Models\AiModel;
use Modules\Category\Models\Category;

class Question extends Model
{
    use HasFactory, SoftDeletes, ArchiveTrait;

    protected $fillable = [
        'question',
        'image',
        'category_id',
        'agent_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function agent() 
    {
        return $this->belongsTo(AiModel::class);
    }
}
