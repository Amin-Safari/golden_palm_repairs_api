<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Melipayamak;
use Exception;

class DeviceController extends Controller
{
    protected function generateUniqueDeviceCode($deviceId, $customerId)
    {
        do {
            $code = sprintf('%s-%04d-%04d',
                Str::upper(Str::random(2)),
                $deviceId,
                $customerId
            );
        } while (Device::where('verification_code', $code)->exists());

        return $code;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $devices = Device::all();
        return response()->json($devices);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // اعتبارسنجی اطلاعات کاربر
        $userValidator = Validator::make($request->all(), [
            'mobile' => 'required|string|max:11',
            'name' => 'required|string|max:255',
        ]);

        // اعتبارسنجی اطلاعات دستگاه
        $deviceValidator = Validator::make($request->all(), [
            'technician_id' => 'nullable|exists:users,id',
            'device_category_id' => 'required|exists:device_categories,id',
            'device_name' => 'required|string|max:255',
            'appearance_details' => 'nullable|string',
            'prepaid' => 'nullable|numeric',
            'delivered_at' => 'required|date'
        ]);

        if ($deviceValidator->fails() || $userValidator->fails()) {
            return response()->json([
                'device_errors' => $deviceValidator->errors(),
                'user_errors' => $userValidator->errors()
            ], 422);
        }

        $mobile = $request->get('mobile');

        // یافتن یا ایجاد کاربر
        $customer = User::where('mobile', $mobile)->first();

        if (!$customer) {
            $customer = User::create([
                'mobile' => $mobile,
                'name' => $request->get('name')
            ]);
        }

        // ایجاد دستگاه
        $deviceData = $deviceValidator->validated();
        $deviceData['customer_id'] = $customer->id;

        $device = Device::create($deviceData);
        $device->verification_code = $this->generateUniqueDeviceCode($device->id, $customer->id);
        $device->save();

        // ارسال پیامک
        try {
            $sms = Melipayamak::sms();
            $to = $mobile;
            $from = '50002710028103';
            $text = 'تست سامانه پیامکی';
            $response = $sms->send($to, $from, $text);
            $json = json_decode($response);

            return response()->json([
                'message' => 'Device created successfully',
                'sms_response' => $json->Value ?? $response,
                'device' => $device
            ]);
        } catch(Exception $e) {
            return response()->json([
                'message' => 'Device created but SMS failed to send',
                'sms_error' => $e->getMessage(),
                'device' => $device
            ], 200);
        }
    }


    /**'customer_id',
     * 'technician_id',
     * 'device_category_id',
     * 'device_name',
     * 'device_problem',
     * 'appearance_details',طاخر
     * 'repair_details',جزیات
     * 'prepaid',پیش
     * 'price',
     * 'status',
     * 'verification_code',
     * 'received_at',دیابت
     * 'delivered_at',تخویل
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $c= Device::find($id);
        return response()->json($c);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $device= Device::find($id);
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $device->update($request->all());


        return response()->json([
            'message' => 'Device category updated successfully',
            'data' => $device
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $device = Device::find($id);
        $device->delete();
        return response()->json(['message' => 'Device category deleted successfully']);
    }
    public function search_device(Request $request)
    {
        $query = $request->input('query');
        $results = User::Where('mobile', 'like',"%{$query}%")
            ->firstOrFail();
        if ($results){
            $device = Device::Where('customer_id', 'like',$results);
            return response()->json($device);
        }
        $device = Device::Where('device_name', 'like',"%{$query}%")->get();
        return response()->json($device);
    }
    public function search_cdevice(Request $request)
    {
        $id_c = $request->input('query');
        $results = Device::Where('device_category_id', 'like',$id_c)->get();
        return response()->json($results);
    }
    public function search_date(Request $request)
    {
        $validated = Validator::make($request->all(),[
            'start_date'=>'required|date',
            'end_date'=>'required|date|after_or_equal:start_date'
        ]);
        if ($validated->fails()){
            return response()->json($validated->errors());
        }
        $results = Device::whereBetweer('received_at',[
            $validated->start_date,
            $validated->end_date
        ])->get();
        return response()->json($results);
    }
}
