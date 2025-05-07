<?php

namespace Modules\AiModel\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\AiModel\Models\AiModel;

class AiModelController extends Controller
{
    public function index()
    {
        $models = AiModel::paginate();
        return $this->respondOk($models, 'Models fetched successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'model_identifier' => 'required|string',
        ]);

        $model = AiModel::create($data);
        return $this->respondCreated($model, 'Model created successfully');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'model_identifier' => 'required|string',
        ]);

        $model = AiModel::find($id);
        if (!$model) {
            return $this->respondError(null, 'Model not found');
        }
        $model->update($data);
        return $this->respondOk($model, 'Model updated successfully');
    }

    public function destroy($id)
    {
        $model = AiModel::find($id);
        if (!$model) {
            return $this->respondError(null, 'Model not found');
        }
        $model->delete();
        return $this->respondOk(null, 'Model deleted successfully');
    }
}
