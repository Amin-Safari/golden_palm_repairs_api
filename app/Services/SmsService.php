<?php

namespace App\Services;

use Melipayamak\Laravel\Facade as Melipayamak;

class SmsService
{
    protected $from = '50002710028103';

    public function sendVerificationCode($phoneNumber, $code, $name)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} عزیز کد ورود شما این است: {$code} /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    public function sendDeviceRegistered($phoneNumber, $name, $deviceDescription)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} دستگاه {$deviceDescription} شما با موفقیت ثبت شد /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    public function sendDeviceDeleted($phoneNumber, $name, $deviceDescription)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} دستگاه {$deviceDescription} شما با موفقیت حذف شد /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    public function sendDeviceReady($phoneNumber, $name, $deviceDescription)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} عزیز دستگاه {$deviceDescription} شما تعمیر شد جهت دریافت به فروشگاه نخل طلایی مراجعه کنید /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    public function sendRepairRegistered($phoneNumber, $name, $deviceName, $verificationCode)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} عزیز دستگاه {$deviceName} شما با موفقیت ثبت شد. کد پیگیری شما: {$verificationCode} /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }

    public function sendRepairDeleted($phoneNumber, $name, $deviceName)
    {
        try {
            $sms = Melipayamak::sms();
            $text = "آقای {$name} درخواست تعمیر دستگاه {$deviceName} شما با موفقیت حذف شد /n تعمیرات نخل طلایی";
            $response = $sms->send($phoneNumber, $this->from, $text);
            $json = json_decode($response);

            return [
                'success' => true,
                'message' => 'پیامک با موفقیت ارسال شد',
                'data' => $json
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'خطا در ارسال پیامک: ' . $e->getMessage()
            ];
        }
    }
}
