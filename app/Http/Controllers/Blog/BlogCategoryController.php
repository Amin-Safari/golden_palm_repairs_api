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
            $this->fail($v);
        }
        $blog_category= BlogCategory::create($v->validate());
        return $this->add('دسته وبلاگ',$blog_category);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $blog_category = BlogCategory::find($id);
        if (!$blog_category){
            return $this->found();
        }
        return response()->json($blog_category,200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $blog_category =BlogCategory::find($id);
        if (!$blog_category){
            return $this->found();
        }
        $v = Validator::make($request->all(), [
            'name' => 'required|max:225',
            'which_web'=> 'required'
        ]);
        if ($v->fails()){
            return $this->fail($v);
        }
        $blog_category->update($v->validate());
        return $this->up('دسته وبلاگ',$blog_category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        $blog_category = BlogCategory::find($id);
        if (!$blog_category){
            return $this->found();
        }
        $blog_category->delete();
        return response()->json(['message' => 'دسته وبلاگ با موفقیت پاک شد']);
    }
}
