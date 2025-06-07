<?php

namespace App\Http\Controllers;

use App\Models\DeviceDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceDescriptionController extends Controller
{
    public function index()
    {
        $descriptions = DeviceDescription::with('category')->get();
        return response()->json([
            'status' => 'success',
            'data' => $descriptions
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:device_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $description = DeviceDescription::create($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'توضیحات دستگاه با موفقیت ایجاد شد',
            'data' => $description
        ], 201);
    }

    public function update(Request $request, DeviceDescription $deviceDescription)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:device_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $deviceDescription->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'توضیحات دستگاه با موفقیت بروزرسانی شد',
            'data' => $deviceDescription
        ]);
    }

    public function destroy(DeviceDescription $deviceDescription)
    {
        $deviceDescription->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'توضیحات دستگاه با موفقیت حذف شد'
        ]);
    }
}
