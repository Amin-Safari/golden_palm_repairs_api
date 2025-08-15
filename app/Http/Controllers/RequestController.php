<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Services\smsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $Request = Request::all();
        return response()->json($Request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, smsService $smsService)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'request' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('id', $request->technician_id)->value('name');
        $text = "سلام جناب آقای صفری" . "\n" . "یک درخواست از طرف " . $user . "با موضوع" . $request->title . "دارید" . "\n" . "خدمات نخل طلایی";
        $mas = $smsService->sendSms('09123111903', $text);
        $Request = Request::create($request->all());
        return response()->json(['message' => $mas, $Request]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $Request = Request::find($id);
        return response()->json($Request);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $Request = Request::find($id);

        // اگر آیتم پیدا نشود
        if (!$Request) {
            return response()->json(['error' => 'Device category not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:225',
            'request' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $Request->update($request->all());

        return response()->json([
            'message' => 'Request updated successfully',
            'data' => $Request
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $Request = Request::find($id);
        $Request->delete();
        return response()->json(['message' => 'Request deleted successfully']);
    }


}
