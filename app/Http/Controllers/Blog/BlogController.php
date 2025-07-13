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
        if ($v->fails()){
            return $this->fail($v);
        }
        $blog = Blog::create($v->validated());
        return $this->add('وبلاگ', $blog);
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog){
            return $this->found();
        }
        return response()->json($blog->load(['author', 'category']),200);
    }

    public function update(Request $request,$id)
    {
        $blog = Blog::find($id);
        if (!$blog){
            return $this->found();
        }
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
        if ($v->fails()){
            return $this->fail($v);
        }
        $blog->update($v->validated());
        return $this->up('وبلاگ',$blog);
    }

    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog){
            return $this->found();
        }
        $blog->delete();
        return $this->delete('وبلاگ');
    }
}
