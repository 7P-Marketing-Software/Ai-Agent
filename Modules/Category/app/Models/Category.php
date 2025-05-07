<?php

namespace Modules\Category\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AiModel\Models\AiModel;
use Modules\Question\Models\Question;
class Category extends Model
{
    use HasFactory,SoftDeletes,NodeTrait;

    protected $fillable =[
        'name',
        'parent_id',
        'image',
        'agent_id'
    ];

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function agent()
    {
        return $this->belongsTo(AiModel::class);
    }
}
