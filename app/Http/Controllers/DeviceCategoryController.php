<?php

namespace App\Http\Controllers;

use App\Models\DeviceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceCategoryController extends Controller
{
    public function index()
    {
        $categories = DeviceCategory::all();
        return response()->json([
            'status' => 'success',
            'data' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:device_categories',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $category = DeviceCategory::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'دسته‌بندی با موفقیت ایجاد شد',
            'data' => $category
        ], 201);
    }

    public function update(Request $request, DeviceCategory $deviceCategory)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:device_categories,name,' . $deviceCategory->id,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $deviceCategory->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'دسته‌بندی با موفقیت بروزرسانی شد',
            'data' => $deviceCategory
        ]);
    }

    public function destroy(DeviceCategory $deviceCategory)
    {
        $deviceCategory->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'دسته‌بندی با موفقیت حذف شد'
        ]);
    }
}
