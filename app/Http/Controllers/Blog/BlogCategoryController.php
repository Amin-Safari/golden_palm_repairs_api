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
        return response()->json($blog_category,200);
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
            return response()->json([$v->errors(),'message'=>'اطلاعات وارد شده معتبر نیست'], 422);

        }
        $blogCategory= BlogCategory::create($v->validate());
        return response()->json(['message'=>'دسته وبلاگ با موفقیت ایجاد شد','data'=>$blogCategory],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogCategory $blogCategory)
    {
        return response()->json($blogCategory,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,BlogCategory $blogCategory)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|max:225',
            'which_web'=> 'required'
        ]);
        if ($v->fails()){
            return response()->json([$v->errors(),'message'=>'اطلاعات وارد شده معتبر نیست'], 422);
        }
        $blogCategory->update($v->validate());
        return response()->json([
            'message' => 'دسته وبلاگ با موفقیت به روزرسانی شد',
            'data' => $blogCategory
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogCategory $blogCategory)
    {
        $blog_category->delete();
        return response()->json(['message' => 'دسته وبلاگ با موفقیت پاک شد']);
    }
}
