<?php

namespace Modules\Category\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Category\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::defaultOrder()->get()->toTree();
        return $this->respondOk($categories, 'Categories fetched successfully');
    }

    public function show($id)
    {
        $category = Category::with([
            'questions',
            'children' => function ($q) {
                $q->with([
                    'questions',
                    'children' => function ($qq) {
                        $qq->with('questions');
                    }
                ]);
            }
        ])->find($id);
        
        if(!$category){
            return $this->respondError(null, 'Category not found');
        }
        return $this->respondOk($category, 'Category fetched successfully');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'parent_id' => 'nullable|exists:categories,id',
            'agent_id'  => [
                'nullable',
                'exists:ai_models,id',
                Rule::requiredIf(function () use ($request) {
                    if ($request->parent_id) {
                        $parent = Category::find($request->parent_id);
                        // Check if the parent has a parent — means we're creating a child of a child
                        return $parent && $parent->parent_id;
                    }
                    return false;
                }),
            ],
        ]);

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = 'Category' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('Category', $imageName, 'public');
            $data['image'] = Storage::url($path);
        }
        $category = Category::create($data);

        return $this->respondCreated($category, 'Category created successfully');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'parent_id' => 'nullable|exists:categories,id',
            'agent_id' => 'nullable|exists:ai_models,id',
        ]);

        $category = Category::find($id);
        if(!$category){
            return $this->respondError(null, 'Category not found');
        }
        
        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName = 'Category' . time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('Category', $imageName, 'public');
            $data['image'] = Storage::url($path);
        }
        $category->update($data);

        return $this->respondOk($category, 'Category updated successfully');
    }

    public function destroy($id)
    {
        $category = Category::find($id);
        if(!$category){
            return $this->respondError(null, 'Category not found');
        }
        $category->delete();

        return $this->respondOk(null, 'Category deleted successfully');
    }

    public function makeRoot($id)
    {
        $category = Category::find($id);
        if(!$category){
            return $this->respondError(null, 'Category not found');
        }
        $category->makeRoot();

        return $this->respondOk($category, 'Category made root successfully');
    }

}
