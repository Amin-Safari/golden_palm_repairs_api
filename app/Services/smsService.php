<?php

namespace App\Services;

class smsService
{
    protected string $apiUrl;
    protected string $apiUrla;
    protected string $apiKey;
    private string $fromNumber;

    public function __construct()
    {
        $this->apiUrl = 'https://console.melipayamak.com/api/send/simple/ca25fdda0f6043f1b45d4efbf71708a1';
        $this->apiUrla = 'https://console.melipayamak.com/api/send/advanced/ca25fdda0f6043f1b45d4efbf71708a1';
        $this->apiKey = 'ca25fdda0f6043f1b45d4efbf71708a1';
        $this->fromNumber = '50002710028103';
    }

    public function sendSms(string $to, string $text): array
    {
        $data = [
            'from' => $this->fromNumber,
            'to' => $to,
            'text' => $text
        ];

        $response = $this->makeRequest($data);

        return $this->handleResponse($response);
    }

    private function makeRequest(array $data): array
    {
        $dataString = json_encode($data);

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $dataString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString),
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_SSL_VERIFYPEER => !app()->isLocal()
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'result' => $result,
            'error' => $error,
            'http_code' => $httpCode
        ];
    }

    private function handleResponse(array $response): array
    {
        if ($response['error']) {
            return [
                'success' => false,
                'message' => 'Curl error: ' . $response['error']
            ];
        }

        $decodedResult = json_decode($response['result'], true);

        if ($response['http_code'] !== 200) {
            return [
                'success' => false,
                'message' => 'API request failed',
                'response' => $decodedResult
            ];
        }

        return [
            'success' => true,
            'data' => $decodedResult
        ];
    }
    public function sendBulkSms(array $recipients, string $message): array
    {
        $data = [
            'from' => $this->fromNumber,
            'to' => $recipients,
            'text' => $message,
            'udh' => '' // User Data Header (برای پیامک‌های باینری)
        ];

        return $this->amakeApiRequest($data);
    }

    private function amakeApiRequest(array $data): array
    {
        $dataString = json_encode($data);

        $ch = curl_init($this->apiUrla);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $dataString,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($dataString),
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_SSL_VERIFYPEER => !app()->isLocal(),
            CURLOPT_TIMEOUT => 30
        ]);

        $result = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $this->ahandleResponse($result, $error, $httpCode);
    }

    private function ahandleResponse($result, $error, $httpCode): array
    {
        if ($error) {
            return [
                'success' => false,
                'message' => 'Curl error: ' . $error,
                'http_code' => $httpCode
            ];
        }

        $decodedResult = json_decode($result, true);

        if ($httpCode !== 200) {
            return [
                'success' => false,
                'message' => 'API request failed',
                'response' => $decodedResult,
                'http_code' => $httpCode
            ];
        }

        return [
            'success' => true,
            'data' => $decodedResult,
            'http_code' => $httpCode
        ];
    }

}
