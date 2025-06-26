<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\DeviceCategory;
use App\Models\DeviceDescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceDescriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $d=DeviceDescription::all();
        return response()->json($d);

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:225',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:device_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $device_category = DeviceDescription::create($request->all());
        return response()->json($device_category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $v= DeviceDescription::find($id);
        return response()->json($v);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $device_description = DeviceDescription::find($id);
        if (!$device_description) {
            return response()->json(['error' => 'Device description not found'], 404);
        }
        $v=Validator::make($request->all(),[
            'description'=>'required|max:225',
            'price'=>'required|numeric',
            'category_id' => 'required|exists:device_categories,id'
        ]);

        if ($v->fails()) {
            return response()->json($v->errors(), 422);
        }
        $device_description->update($request->all());

        return response()->json([
            'message' => 'Device category updated successfully',
            'data' => $device_description
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device_category = DeviceDescription::find($id);
        $device_category->delete();
        return response()->json(['message' => 'Device category deleted successfully']);
    }
}
