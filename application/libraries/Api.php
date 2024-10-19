<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api
{
    private $baseUrl = 'https://sandbox.partner.api.bri.co.id';
    private $client_id = 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh';
    private $client_secret = 'MNfGscq4w6XUmAp3';
    private $partner_id = 'YGSDev';
    private $channel_id = '00009';
    private $channel_id_mapping = [
        '00001' => 'teller',
        '00002' => 'ATM',
        '00003' => 'IB/NBMB/Brilink Mobile',
        '00004' => 'SMSB',
        '00005' => 'CMS/IBIZ',
        '00006' => 'EDC',
        '00007' => 'RTGS',
        '00008' => 'OTHER',
        '00009' => 'API'
    ];
    private $private_key;
    private $access_token = null;

    public function __construct()
    {
        $this->private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDVX1h6osiKwshHVl2PZYjlGD2JNlKzwKTRdp1H7ayy0Nudyyz2
ZJKVi6weW68QsaKaAMq5UQmcrV18HMzrsOOLH5lJvEwMQ97vep2ZKs0SLq0SaMNr
u6ZdkXzg66sinnbQQSo8XxP1Qeuvj2ZFmwQ18dCpB7JZDPBpo+e/dFPBIQIDAQAB
AoGBAJ4FdumcFRlvGBR9Cd1hPPkt8qTj7mvhiC74wZK7muLzezJpfmscINNQFbCG
Bik+5UVYwMpuEchPPKTmT31eC6Vq8XM5MrNFH4NogPxCnhrQ/Aw8hE2iqpEbr8Ro
jVckKixNqMDoBNgeEAa5LWQiaiDg46C5o2FB6KSAAxQ19SvhAkEA8P/7Rj3ujYiR
72cjUImlyGqela22ruDyG8UQJH4qDMBlwUPxie5Kbm1WmplpszNUbY7jmiEnLvLm
WOhxq71IUwJBAOKnKAHRSe8eKIEFPDyby1gWf9jnqR0vJEkHROX4GgWzdbl4QQoH
qmOMVMXF05gTXRdeadYFgzEwk+oeYTAg0jsCQBjp7ZkCWAHrp2J/YAg4YpoIY6KH
lcYYXQ7/3T5YiJJO5XYIRxUCPFGUHgrXZzTuToEQ73iEit9wnt18Ehw18h8CQQDD
yIabg1DtN8zfHkmZRS6SqeTH1dzUc9tRJfFTAUxhLlL74i+0XUjG8vprWGZd0CQy
woCDuoFH5WFv88waCc/vAkEA5hdTlziUERjiWGTYw+kiB6luINE7Zr0I8/VNyJhS
eLEUUp7Z5I4zDowMZXHXOiIJ2J4xeF8ebNIXsenYp3wEHA==
-----END RSA PRIVATE KEY-----
EOD;
    }

    public function get_access_token()
    {
        $url = $this->baseUrl . '/oauth/client_credential/accesstoken?grant_type=client_credentials';

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode($this->client_id . ':' . $this->client_secret)
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 200) {
            $json = json_decode($response, true);
            return $json['access_token'];
        } else {
            return ['error' => 'Gagal mendapatkan access token. Kode HTTP: ' . $httpCode, 'response' => $response];
        }
    }

    private function get_valid_access_token()
    {
        if ($this->access_token && $this->token_timestamp) {
            $current_time = time();
            if (($current_time - $this->token_timestamp) < 60) {
                return $this->access_token;
            }
        }
        $this->access_token = $this->get_push_notif_token();
        $this->token_timestamp = time();
        return $this->access_token;
    }

    public function get_push_notif_token()
    {
        $path = '/snap/v1.0/access-token/b2b';
        $url = 'https://sandbox.partner.api.bri.co.id' . $path;
        $timezone = new DateTimeZone('+07:00');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:s.vP');
        $body = json_encode([
            'grantType' => 'client_credentials'
        ]);
        $stringToSign = $this->client_id . '|' . $timestamp;
        $privateKey = $this->private_key;
        $privateKeyId = openssl_get_privatekey($privateKey);

        if (!$privateKeyId) {
            throw new Exception('Gagal memuat kunci privat');
        }
        openssl_sign($stringToSign, $signature, $privateKeyId, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKeyId);
        $signatureBase64 = base64_encode($signature);
        $headers = [
            'X-SIGNATURE: ' . $signatureBase64,
            'X-CLIENT-KEY: ' . $this->client_id,
            'X-TIMESTAMP: ' . $timestamp,
            'Content-Type: application/json',
        ];
        $response = $this->send_api_request($url, 'POST', $headers, $body);
        $json = json_decode($response, true);
        return $json['accessToken'];


    }


    public function send_va_payment_notification($partnerServiceId, $customerNo, $virtualAccountNo,$trxDateTime,$paymentRequestId,$paymentAmount)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_valid_access_token();
        $path = '/snap/v1.0/transfer-va/notify-payment-intrabank';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateTime' => $trxDateTime,
            'paymentRequestId' => $paymentRequestId,
            'additionalInfo' => 'SPP 1',
            'idApp' => 'ypgs',
            'passApp' => 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh',
            'paymentAmount' => $paymentAmount,
            'terminalId' => '9',
            'bankId' => '002'

        ];
        $body_json = json_encode($body);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        return json_decode($response, true);
    }



    public function inquiry_payment_va($partnerServiceId, $customerNo, $virtualAccountNo)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_valid_access_token();
        $path = '/snap/v1.0/transfer-va/inquiry-intrabank';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo
        ];
        $body_json = json_encode($body);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        return json_decode($response, true);
    }




    public function payment_va($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $sourceAccountNo, $partnerReferenceNo, $paidAmount, $trxDateTime)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_valid_access_token();
        $path = '/snap/v1.0/transfer-va/payment-intrabank';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'sourceAccountNo' => $sourceAccountNo,
            'partnerReferenceNo' => $partnerReferenceNo,
            'paidAmount' => $paidAmount,
            'trxDateTime' => $trxDateTime,
        ];
        if ($paidAmount) {
            $body['paidAmount'] = [
                'value' => $paidAmount['value'],
                'currency' => $paidAmount['currency'],
            ];
        }
        $body_json = json_encode($body);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        return json_decode($response, true);
    }


    public function inquiry_payment_va_briva($partnerServiceId, $customerNo, $virtualAccountNo, $amount, $currency, $trxDateInit, $sourceBankCode, $inquiryRequestId, $additionalInfo)
{
    $timestamp = gmdate('Y-m-d\TH:i:s\Z');
    $path = '/snap/v1.0/transfer-va/inquiry';
    $token = $this->get_valid_access_token();

    $body = [
        'partnerServiceId' => '   ' . $partnerServiceId,
        'customerNo' => $customerNo,
        'virtualAccountNo' => '   ' . $virtualAccountNo,
        'amount' => $amount,
        'currency' => $currency,
        'trxDateInit' => $trxDateInit,
        'channelCode' => $this->channel_id,
        'sourceBankCode' => $sourceBankCode,
        'passApp' => 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh',
        'inquiryRequestId' => $inquiryRequestId,
        'additionalInfo' => $additionalInfo,
        'idApp' => 'ypgs',
        'partnerUrl' => 'http://127.0.0.1:8000',
        'token' => $token,
        'clientSecret' => $this->client_secret
    ];
    $body_json = json_encode($body, JSON_UNESCAPED_SLASHES);
    $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $token, $body_json);
    $external_id = rand(100000000, 999999999);

    $headers = [
        'Authorization: Bearer ' . $token,
        'X-SIGNATURE: ' . $signature,
        'X-TIMESTAMP: ' . $timestamp,
        'X-PARTNER-ID: ' . $this->partner_id,
        'CHANNEL-ID: ' . $this->channel_id,
        'X-EXTERNAL-ID: ' . $external_id,
        'Content-Type: application/json'
    ];

    $url = 'https://sandbox.partner.api.bri.co.id' . $path;
    $response = $this->send_api_request($url, 'POST', $headers, $body_json);
    return json_decode($response, true);
}







    public function payment_va_briva($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $sourceAccountNo, $partnerReferenceNo, $paidAmount, $trxDateTime)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_valid_access_token();
        $path = '/snap/v1.0/transfer-va/payment-intrabank';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'sourceAccountNo' => $sourceAccountNo,
            'partnerReferenceNo' => $partnerReferenceNo,
            'paidAmount' => $paidAmount,
            'trxDateTime' => $trxDateTime,
        ];
        if ($paidAmount) {
            $body['paidAmount'] = [
                'value' => $paidAmount['value'],
                'currency' => $paidAmount['currency'],
            ];
        }
        $body_json = json_encode($body);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        return json_decode($response, true);
    }






    public function create_virtual_account($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $totalAmountValue, $totalAmountCurrency, $expiredDate, $trxId, $additionalInfo = null)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/create-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'totalAmount' => [
                'value' => number_format($totalAmountValue, 2, '.', ''),
                'currency' => $totalAmountCurrency
            ],
            'expiredDate' => $expiredDate,
            'trxId' => $trxId
        ];

        if ($additionalInfo) {
            $body['additionalInfo'] = [
                'description' => $additionalInfo
            ];
        }

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
    }

    public function inquiry_virtual_account($partnerServiceId, $customerNo, $virtualAccountNo, $trxId = null)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/inquiry-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo
        ];

        if ($trxId) {
            $body['trxId'] = $trxId;
        }

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
    }


    public function update_status_va($partnerServiceId, $customerNo, $virtualAccountNo, $trxId, $paidStatus)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/update-status';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId,
            'paidStatus' => $paidStatus
        ];

        if ($trxId) {
            $body['trxId'] = $trxId;
        }

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'PUT', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'PUT', $headers, $body_json);

        return json_decode($response, true);
    }

    public function update_va($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $totalAmountValue, $totalAmountCurrency, $expiredDate, $trxId, $additionalInfo = null)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/update-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'totalAmount' => [
                'value' => $totalAmountValue,
                'currency' => $totalAmountCurrency
            ],
            'expiredDate' => $expiredDate,
            'trxId' => $trxId
        ];

        if ($additionalInfo) {
            $body['additionalInfo'] = [
                'description' => $additionalInfo
            ];
        }

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'PUT', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'PUT', $headers, $body_json);

        return json_decode($response, true);
    }

    public function push_notif($partnerServiceId, $customerNo, $virtualAccountNo, $trxDateTime, $paymentRequestId)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = 'snap/v1.0/transfer-va/notify-payment-intrabank';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateTime' => $trxDateTime,
            'paymentRequestId' => $paymentRequestId,
            'additionalInfo' => [
                'idApp' => '24123244',
                'passApp' => '354324134',
                'paymentAmount' => '650000',
                'terminalId' => '1',
                'bankId' => '002'
            ]
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
    }

    public function delete_va($partnerServiceId, $customerNo, $virtualAccountNo, $trxId)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/delete-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId,
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'DELETE', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'DELETE', $headers, $body_json);

        return json_decode($response, true);
    }



    public function get_report_va($partnerServiceId, $startDate, $startTime, $endTime)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/report';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'startDate' => $startDate,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
    }


    public function inquiry_status_va($partnerServiceId, $customerNo, $virtualAccountNo, $inquiryRequestId)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $access_token = $this->get_access_token();

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/snap/v1.0/transfer-va/status';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'inquiryRequestId' => $inquiryRequestId
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $access_token, $body_json);

        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
    }

    private function generate_rsa_signature($payload)
    {
        $private_key = openssl_pkey_get_private($this->private_key);
        if (!$private_key) {
            return ['error' => 'Failed to load private key'];
        }
        openssl_sign($payload, $signature, $private_key, OPENSSL_ALGO_SHA256);
        openssl_free_key($private_key);
        return base64_encode($signature);
    }

    private function generate_hmac_signature_push_notif($method, $path, $token, $timestamp, $body)
    {
        $payload = $method . ':' . $path . ':' . $token . ':' . $timestamp . ':' . hash('sha256', $body);
        return hash_hmac('sha512', $payload, $this->client_secret);
    }

    private function generate_hmac_signature($path, $method, $timestamp, $token, $body)
    {
        $payload = $method . ':' . $path . ':' . $token . ':' . hash('sha256', $body) . ':' . $timestamp;
        return hash_hmac('sha512', $payload, $this->client_secret);
    }

    private function send_api_request($url, $method = 'POST', $headers = [], $body = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;

            case 'PUT':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;

            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;

            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
                break;

            case 'GET':
                break;

            default:
                return ['error' => 'Unsupported HTTP method'];
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'CURL Error: ' . $error];
        }

        curl_close($ch);
        return $response;
    }

}
