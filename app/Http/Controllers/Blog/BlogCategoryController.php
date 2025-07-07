<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BlogCategory;
use Illuminate\Support\Facades\Validator;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blog_category = BlogCategory::all();
        return response()->json($blog_category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'name'=>'required|max:225',
            'which_web'=>'required'
        ]);
        if ($v->fails()){
            return response()->json($v->errors());
        }
        BlogCategory::created($v);
        return response()->json('success');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $blog_category = BlogCategory::find($id);
        return response()->json($blog_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $blog_category = BlogCategory::find($id);
        if (!$blog_category) {
            return response()->json(['error' => 'Device category not found'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:225',
            'which_web'=> 'required'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(), 422);
        }
        $blog_category->update($request->all());
        return response()->json([
            'message' => 'Device category updated successfully',
            'data' => $blog_category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $blog_category= BlogCategory::find($id);
        $blog_category->delete();
        return response()->json(['message' => 'Device category deleted successfully']);
    }
}
