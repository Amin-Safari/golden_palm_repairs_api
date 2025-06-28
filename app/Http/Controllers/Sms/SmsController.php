<?php

namespace App\Http\Controllers\Sms;

use App\Http\Controllers\Controller;
use App\Models\Sms;
use App\Services\smsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SmsController extends Controller
{
    public function sendSms(Request $request,smsService $smsService)
    {
        $validate = Validator::make($request->all(),[
            'mobile'=> 'required|array',
            'text'=> 'required|string'
        ]);
        if ($validate->fails()){
            return response()->json($validate->errors());
        }
        $mas = $smsService->sendBulkSms($request->mobile,$request->text);
        return response()->json(['message'=>$mas]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sms = Sms::all();
        return response()->json($sms);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body'=> 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $sms = Sms::create($request->all());
        return response()->json($sms);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $sms = Sms::find($id);
        return response()->json($sms);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $sms = Sms::find($id);

        // اگر آیتم پیدا نشود
        if (!$sms) {
            return response()->json(['error' => 'SMS not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'body'=> 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $sms->update($request->all());

        return response()->json([
            'message' => 'SMS updated successfully',
            'data' => $sms
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sms = Sms::find($id);
        $sms->delete();
        return response()->json(['message' => 'SMS deleted successfully']);
    }

}
