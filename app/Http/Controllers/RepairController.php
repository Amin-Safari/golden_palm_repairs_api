<?php

// app/Http/Controllers/RepairController.php

namespace App\Http\Controllers;

use App\Http\Requests\RepairRequest;
use App\Http\Resources\RepairResource;
use App\Models\Repair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RepairController extends Controller
{
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

    public function store(RepairRequest $request)
    {
        $data = $request->validated();
        $data['customer_id'] = auth()->id();
        $data['received_at'] = now();
        $data['verification_code'] = rand(1000, 9999);

        $repair = Repair::create($data);

        // ارسال SMS به مشتری
        // SMS::send($repair->customer->phone, "درخواست تعمیر شما با کد {$repair->id} ثبت شد.");

        return new RepairResource($repair);
    }

    public function show(Repair $repair)
    {
        $this->authorize('view', $repair);
        return new RepairResource($repair->load(['customer', 'technician', 'category']));
    }

    public function update(RepairRequest $request, Repair $repair)
    {
        $this->authorize('update', $repair);
        
        $repair->update($request->validated());
        
        return new RepairResource($repair);
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
            // SMS::send($repair->customer->phone, $messages[$repair->status]);
        }
    }
}
