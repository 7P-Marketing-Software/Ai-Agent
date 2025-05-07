<?php

namespace Modules\AiModel\Models;

use App\Http\Traits\ArchiveTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Category\Models\Category;
use Modules\Question\Models\Question;

class AiModel extends Model
{
    use HasFactory, SoftDeletes, ArchiveTrait;

    protected $fillable = ['model_identifier'];

    public function questions()
    {
        return $this->hasMany(Question::class, 'agent_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'agent_id');
    }
}
