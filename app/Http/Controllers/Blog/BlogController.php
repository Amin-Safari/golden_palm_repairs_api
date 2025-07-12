<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['author', 'category'])->get();
        return response()->json($blogs,200);
    }

    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:225',
            'tags' => 'required|string|max:225',
            'body' => 'required|string',
            'time_to_read' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image_path' => 'required|max:225',
            'video_path' => 'nullable',
            'audio_path' => 'nullable'
        ]);

        if ($v->fails()) {
            return response()->json([$v->errors(),'message'=>'اطلاعات وارد شده معتبر نیست'], 422);
        }

        $blog = Blog::create($v->validated());
        return response()->json(['message' => 'وبلاگ با موفقیت ایجاد شد', 'data' => $blog],201);
    }

    public function show(Blog $blog)
    {
        return response()->json($blog->load(['author', 'category']),200);
    }

    public function update(Request $request, Blog $blog)
    {
        $v = Validator::make($request->all(), [
            'title' => 'required|string|max:225',
            'tags' => 'required|string|max:225',
            'body' => 'required|string',
            'time_to_read' => 'required|integer',
            'author_id' => 'required|exists:authors,id',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'image_path' => 'required|max:225',
            'video_path' => 'nullable',
            'audio_path' => 'nullable'
        ]);

        if ($v->fails()) {
            return response()->json([$v->errors(),'message'=>'اطلاعات وارد شده معتبر نیست'], 422);
        }

        $blog->update($v->validated());
        return response()->json(['message' => 'بلاگ با موفقیت به روزرسانی شد', 'data' => $blog],200);
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return response()->json(['message' => 'بلاگ با موفقیت حذف شد'],200);
    }
}
