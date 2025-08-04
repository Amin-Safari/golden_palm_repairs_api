<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\smsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Melipayamak;
use Exception;

class DeviceController extends Controller
{
    protected function generateUniqueDeviceCode($deviceId)
    {
        do {
            $code = sprintf('%s-%05d',
                Str::upper(Str::random(2)),
                $deviceId
            );
        } while (Device::where('verification_code', $code)->exists());

        return $code;
    }

    public function index()
    {
        $devices = Device::with(['customer', 'technician', 'category'])->get();
        return response()->json($devices);
    }

    public function store(Request $request, smsService $smsService)
    {
        $userValidator = Validator::make($request->all(), [
            'mobile' => 'required|size:11',
            'name' => 'required|string|max:255',
        ]);

        $deviceValidator = Validator::make($request->all(), [
            'technician_id' => 'nullable|exists:users,id',
            'device_category_id' => 'required|exists:device_categories,id',
            'device_name' => 'required|string|max:255',
            'appearance_details' => 'nullable|string',
            'prepaid' => 'nullable|numeric',
            'delivered_at' => 'required|'
        ]);

        if ($deviceValidator->fails() || $userValidator->fails()) {
            return response()->json([
                'errors' => array_merge(
                    $deviceValidator->errors()->toArray(),
                    $userValidator->errors()->toArray()
                )
            ], 422);
        }

        try {
            $customer = User::firstOrCreate(
                ['mobile' => $request->mobile],
                ['name' => $request->name]
            );

            $device = Device::create(array_merge(
                $deviceValidator->validated(),
                ['customer_id' => $customer->id]
            ));

            $device->update([
                'verification_code' => $this->generateUniqueDeviceCode($device->id)
            ]);
            // ارسال پیامک
            try {
                $text = " سلام " . $request->name . " عزیز " . "\n" . " دستگاه " . $request->device_name . " با کد رهگیری: " . "\"" . $device->verification_code . "\"" . " با موفقیت ثبت شد" . "\n" . "تعمیرات نخل طلایی";
                $response = $smsService->sendSms($request->mobile, $text);

                return response()->json([
                    'message' => 'Device created and SMS sent successfully',
                    'device' => $device,
                    'sms_response' => $response
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Device created but SMS failed to send',
                    'device' => $device,
                    'sms_error' => $e->getMessage()
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create device',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(string $id)
    {
        $device = Device::with(['customer', 'technician', 'category'])->find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        return response()->json($device);
    }

    public function update(Request $request, string $id, smsService $smsService)
    {
        $device = Device::find($id);
        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }
        $Cid = $device->customer_id;
        $user = User::where('id', $Cid)->value('name');
        $mobile = User::where('id', $Cid)->value('mobile');
        $device->update($request->all());

        if ($device->status == 'fixed') {
            $text = " سلام " . $user . " عزیز " . "\n" . " دستگاه " . $device->device_name . " با کد رهگیری: " . "\"" . $device->verification_code . "\"" . " آماده تحویل است. " . "\n" . "تعمیرات نخل طلایی";
            $smsService->sendSms($mobile, $text);
        }
        return response()->json([
            'message' => 'Device category updated successfully',
            'data' => $device
        ]);
    }


    public function destroy(string $id, smsService $smsService)
    {
        $device = Device::find($id);

        if (!$device) {
            return response()->json(['message' => 'Device not found'], 404);
        }

        $Cid = $device->customer_id;
        $user = User::where('id', $Cid)->value('name');
        $mobile = User::where('id', $Cid)->value('mobile');
        $text = " سلام " . $user . " عزیز " . "\n" . " دستگاه " . $device->device_name . " با کد رهگیری: " . "\"" . $device->verification_code . "\"" . " حذف شد

        . " . "\n" . "تعمیرات نخل طلایی";
        $smsService->sendSms($mobile, $text);

        $device->delete();

        return response()->json(['message' => 'Device deleted successfully']);
    }

    public function search_device(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:3'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = $request->input('query');

        // جستجو بر اساس موبایل کاربر
        $user = User::where('mobile', 'like', "%{$query}%")->first();

        if ($user) {
            $devices = Device::where('customer_id', $user->id)
                ->with(['category', 'technician'])
                ->get();

            return response()->json($devices);
        }

        // جستجو بر اساس نام دستگاه
        $devices = Device::where('device_name', 'like', "%{$query}%")
            ->with(['category', 'technician', 'customer'])
            ->get();

        return response()->json($devices);
    }

    public function search_cdevice(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|exists:device_categories,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $devices = Device::where('device_category_id', $request->input('query'))
            ->with(['category', 'technician', 'customer'])
            ->get();

        return response()->json($devices);
    }

    public function search_date(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $devices = Device::whereBetween('received_at', [
            $request->start_date,
            $request->end_date
        ])->with(['category', 'technician', 'customer'])
            ->get();

        return response()->json($devices);
    }

    public function countFixed()
    {
        $count = Device::where('status', 'fixed')->count();
        return response()->json($count);
    }

    public function countDeliveredWData()
    {
        $count = Device::whereBetween('delivered_real_at', [
            Carbon::today(),
            Carbon::tomorrow()
        ])->count();
        return response()->json($count);
    }

    public function countFixing()
    {
        $count = Device::where('status', 'received')->count();
        return response()->json($count);
    }

    public function fixedDevices()
    {
        $devices = Device::where('status', 'fixed')
            ->with(['customer', 'technician']) // اگر رابطه‌هایی وجود دارد
            ->get();

        return response()->json($devices);
    }
}
