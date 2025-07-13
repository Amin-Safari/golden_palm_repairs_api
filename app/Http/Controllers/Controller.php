<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    public function add($txt ,$data)
    {
        return response()->json([
            'message'=>'با موفقیت ایجاد شد'. $txt,
            'data'=>$data
        ], 201);
    }
    public function up($txt ,$data )
    {
        return response()->json([
            'message'=>'با موفقیت به روزرسانی شد'. $txt,
            'data'=>$data
        ], 200);
    }
    public function delete($txt)
    {
        return response()->json([
            'message'=>'با موفقیت پاک شد'. $txt,
        ], 200);
    }
    public function fail($v)
    {
        return response()->json([
            'message'=>'اطلاعات وارد شده معتبر نیست',
            'errors'=>$v->errors()
        ], 422);
    }
    public function found()
    {
        return response()->json([
            'message'=>'صفحه مورد نظر یافت نشد'
        ],404);
    }
}
