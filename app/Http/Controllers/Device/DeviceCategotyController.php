<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\DeviceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceCategotyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $category =DeviceCategory::all();
        return response()->json($category);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json( $validator->errors(), 422);
        }

        $device_category = DeviceCategory::create($request->all());
        return response()->json($device_category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $device_category = DeviceCategory::find($id);
        return response()->json($device_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $device_category = DeviceCategory::find($id);

        // اگر آیتم پیدا نشود
        if (!$device_category) {
            return response()->json(['error' => 'Device category not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:225'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $device_category->update($request->only(['name']));

        return response()->json([
            'message' => 'Device category updated successfully',
            'data' => $device_category
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device_category = DeviceCategory::find($id);
        $device_category->delete();
        return response()->json(['message' => 'Device category deleted successfully']);
    }
}
