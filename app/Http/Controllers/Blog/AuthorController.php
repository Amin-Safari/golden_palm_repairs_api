<?php

namespace App\Http\Controllers\Blog;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Models\DeviceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthorController extends Controller
{
    public function index()
    {
        $author = Author::all();
        return response()->json($author,200);
    }
    public function store(Request $request)
    {
        $v = Validator::make($request->all(),[
            'name' => 'required|max:225',
            'skill' => 'required|max:225',
            'slogan' => 'required|max:225',
            'about' => 'required',
            'author_photo' => 'required|image|max:1024'
        ]);
        if ($v->fails()){
            return $this->fail($v);
        }
        $author = Author::create($v->validate());
        return $this->add("نویسنده",$author);
    }
    public function show(string $id)
    {
        $author = Author::find($id);
        if (!$author){
            return $this->found();
        }
        return response()->json($author);
    }
    public function update(string $id, Request $request)
    {
        $author = Author::find($id);
        if (!$author){
            return $this->found();
        }
        $v = Validator::make($request->all(),[
            'name' => 'required|max:225',
            'skill' => 'required|max:225',
            'slogan' => 'required|max:225',
            'about' => 'required',
            'author_photo' => 'required|image|max:1024'
        ]);
        if ($v->fails()){
            return $this->fail($v);
        }
        $author->update($request->all());
        return $this->up('نویسنده',$author);
    }
    public function destroy(string $id)
    {
        $author = Author::find($id);
        if (!$author){
            return $this->found();
        }
        $author->delete();
        return $this->delete('نویسنده');
    }
}
