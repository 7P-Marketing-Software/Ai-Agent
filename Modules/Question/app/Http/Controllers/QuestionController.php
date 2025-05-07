<?php

namespace Modules\Question\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Question\Models\Question;
use Modules\Category\Models\Category;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::paginate();
        return $this->respondOk($questions, 'Questions fetched successfully');
    }

    public function show($id)
    {
        $question = Question::with(['category','agent'])->find($id);
        if(!$question){
            return $this->respondError(null, 'Question not found');
        }
        return $this->respondOk($question, 'Question fetched successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'question' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'agent_id' => 'required|exists:ai_models,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = 'Question' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('Question', $imageName, 'public');
            $data['image'] = Storage::url($path);
        }

        $question = Question::create($data);

        return $this->respondCreated($question, 'Question created successfully');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'question' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'agent_id' => 'nullable|exists:ai_models,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $question = Question::find($id);
        if(!$question){ 
            return $this->respondError(null, 'Question not found');
        }

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = 'Question' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('Question', $imageName, 'public');
            $data['image'] = Storage::url($path);
        }

        $question->update($data);

        return $this->respondOk($question, 'Question updated successfully');
    }

    public function destroy($id)
    {
        $question = Question::find($id);
        if(!$question){
            return $this->respondError(null, 'Question not found');
        }

        $question->delete();

        return $this->respondOk(null, 'Question deleted successfully');
    }
    
    
}
