<?php

// app/Http/Controllers/RepairController.php

namespace App\Http\Controllers;

use App\Http\Requests\RepairRequest;
use App\Http\Resources\RepairResource;
use App\Models\Repair;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class RepairController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $user = auth()->user();

        // فیلتر بر اساس نقش کاربر
        $repairs = Repair::when($user->hasRole('technician'), function ($query) use ($user) {
            return $query->where('technician_id', $user->id);
        })
            ->when($user->hasRole('customer'), function ($query) use ($user) {
                return $query->where('customer_id', $user->id);
            })
            ->with(['customer', 'technician', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return RepairResource::collection($repairs);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
            'name' => 'required|string|max:255',
            'device_category_id' => 'required|exists:device_categories,id',
            'device_name' => 'required|string|max:255',
            'device_problem' => 'required|string',
            'appearance_details' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            $customerRole = Role::where('name', 'customer')->first();
            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'password' => bcrypt(Str::random(8))
            ]);
            $user->assignRole($customerRole);
        }

        $verificationCode = rand(100000, 999999);

        $repair = Repair::create([
            'customer_id' => $user->id,
            'device_category_id' => $request->device_category_id,
            'device_name' => $request->device_name,
            'device_problem' => $request->device_problem,
            'appearance_details' => $request->appearance_details,
            'status' => 'received',
            'verification_code' => $verificationCode,
            'received_at' => now()
        ]);

        // ارسال پیامک ثبت تعمیر
        $this->smsService->sendRepairRegistered(
            $user->mobile,
            $user->name,
            $repair->device_name,
            $verificationCode
        );

        return response()->json([
            'status' => 'success',
            'message' => 'درخواست تعمیر با موفقیت ثبت شد',
            'data' => $repair->load(['customer', 'deviceCategory'])
        ], 201);
    }

    public function show(Repair $repair)
    {
        $this->authorize('view', $repair);
        return new RepairResource($repair->load(['customer', 'technician', 'category']));
    }

    public function update(Request $request, Repair $repair)
    {
        $validator = Validator::make($request->all(), [
            'technician_id' => 'nullable|exists:users,id',
            'repair_details' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'status' => 'required|in:received,in_progress,ready,delivered'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $oldStatus = $repair->status;
        $repair->update($request->all());

        // اگر وضعیت به ready تغییر کرده باشد، پیامک ارسال شود
        if ($request->status === 'ready' && $oldStatus !== 'ready') {
            $repair->load('customer');
            $this->smsService->sendDeviceReady(
                $repair->customer->mobile,
                $repair->customer->name,
                $repair->device_name
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'درخواست تعمیر با موفقیت بروزرسانی شد',
            'data' => $repair->load(['customer', 'technician', 'deviceCategory'])
        ]);
    }

    public function destroy(Repair $repair)
    {
        // ذخیره اطلاعات قبل از حذف برای ارسال پیامک
        $repair->load('customer');
        $user = $repair->customer;
        $deviceName = $repair->device_name;

        $repair->delete();

        // ارسال پیامک حذف درخواست تعمیر
        $this->smsService->sendRepairDeleted(
            $user->mobile,
            $user->name,
            $deviceName
        );

        return response()->json([
            'status' => 'success',
            'message' => 'درخواست تعمیر با موفقیت حذف شد'
        ]);
    }

    public function verifyCode(Request $request, Repair $repair)
    {
        $validator = Validator::make($request->all(), [
            'verification_code' => 'required|numeric|digits:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($repair->verification_code != $request->verification_code) {
            return response()->json([
                'status' => 'error',
                'message' => 'کد تایید نامعتبر است'
            ], 400);
        }

        $repair->update([
            'status' => 'delivered',
            'delivered_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'کد تایید با موفقیت تایید شد و دستگاه تحویل داده شد'
        ]);
    }

    public function updateStatus(Request $request, Repair $repair)
    {
        $this->authorize('updateStatus', $repair);

        $request->validate([
            'status' => 'required|in:received,in_progress,ready_for_delivery,delivered,canceled'
        ]);

        $repair->update(['status' => $request->status]);

        // ارسال نوتیفیکیشن بر اساس وضعیت جدید
        $this->sendStatusNotification($repair);

        return new RepairResource($repair);
    }

    protected function sendStatusNotification(Repair $repair)
    {
        $messages = [
            'in_progress' => "تعمیر دستگاه شما شروع شده است.",
            'ready_for_delivery' => "دستگاه شما آماده تحویل است. کد تأیید: {$repair->verification_code}",
            'delivered' => "دستگاه شما تحویل داده شد.",
            'canceled' => "درخواست تعمیر شما لغو شد."
        ];

        if (isset($messages[$repair->status])) {
            $this->smsService->sendStatusNotification($repair->customer->mobile, $messages[$repair->status]);
        }
    }
}
