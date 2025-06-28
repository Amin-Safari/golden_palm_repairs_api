<?php

namespace App\Services;

class smsService
{
    protected string $apiUrl;
    protected string $apiKey;
    private string $fromNumber;

    public function __construct()
    {
        $this->apiUrl = 'https://console.melipayamak.com/api/send/simple/ca25fdda0f6043f1b45d4efbf71708a1';
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

}
