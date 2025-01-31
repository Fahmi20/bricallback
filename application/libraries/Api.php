<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api
{
    private $baseUrl = 'https://apidevportal.aspi-indonesia.or.id:44310';

    private $public_key;
    private $public_key_path = '/mnt/data/pubkey.pem';
    private $client_id_push_notif = '8kPf12Bc3HxY47RgQwZ5jT6UvRz1';
    private $client_secret_push_notif = 'Bf45NzPq09XwSa1RtU6Vg8MjYt4R';
    private $token_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/access-token/b2b";
    private $notif_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/transfer-va/notify-payment-intrabank";
    private $public_key_pem = "-----BEGIN PUBLIC KEY-----3067b8c9c44e4f1b88a20ce3e2271c6a-----END PUBLIC KEY-----"; //MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyH96OWkuCmo+VeJAvOOweHhhMZl2VPT9zXv6zr3a3CTwglmDcW4i5fldDzOeL4aco2d+XrPhCscrGKJA4wH1jyVzNcHK+RzsABcKtcqJ4Rira+x02/f554YkXSkxwqqUPtmCMXyr30FCuY3decIu2XsB9WYjpxuUUOdXpOVKzdCrABvZORn7lI2qoHeZ+ECytVYAMw7LDPOfDdo6qnD5Kg+kzVYZBmWC79TW9MaLkLLWNzY7XDe8NBV1KNU+G9/Ktc7S2+fF5jvPc+CWG7CAFHNOkAxyHZ7K1YvA4ghOckQf4EwmxdmDNmEk8ydYVix/nJXiUBY44olhNKr+EKJhYQIDAQAB
    private $client_id = '4d4776a092ca457e89bd1436f67184a8'; //G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh

    private $client_secret = 'LyV9XytLCLNOXbmdIaXh9zl4i+PI2mSsXaxU90QR94E='; //'MNfGscq4w6XUmAp3'
    private $client_secret_push_notif_url = 'Bf45NzPq09XwSa1RtU6Vg8MjYt4R';
    private $partner_id = 'YPGS';
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
    private $private_key = 'Xwk+MG/x7NZuzo01Uuxjfq5W4HSWJtMt1CoWOF3unWQ=';
    private $access_token = null;
    private $last_url;
    private $last_headers;
    private $last_body;

    public function __construct()
    {
        $this->private_key;
        $this->CI =& get_instance();
        $this->publicKeyPath = APPPATH . 'keys/pubkey.pem';


    }

    public function signature_access_token()
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $path = '/api/v1.0/utilities/signature-auth';
        $headers = [
            'Content-Type: application/json',
            'X-TIMESTAMP: ' . $timestamp,
            'X-CLIENT-KEY: ' . $this->client_id,
            'Private_Key: ' . $this->private_key,
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers);
        $json = json_decode($response, true);
        return $json['signature'];
    }

    public function signature_request($accessToken, $HttpMethod, $EndpoinUrl, $body, $timestamp)
    {
        $HttpMethodRequset = 'POST';
        $path = '/api/v1.0/utilities/signature-service';
        $headers = [
            'Content-Type: application/json',
            'X-TIMESTAMP: ' . $timestamp,
            'X-CLIENT-SECRET: ' . $this->client_secret,
            'HttpMethod: ' . $HttpMethod,
            'EndpoinUrl: ' . $EndpoinUrl,
            'AccessToken: ' . $accessToken
        ];
        $url = $this->baseUrl . $path;
        $body_json = json_encode($body);
        $response = $this->send_api_request($url, $HttpMethodRequset, $headers, $body_json);
        $json = json_decode($response, true);
        return $json['signature'];
    }
    public function get_access_token()
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $path = '/api/v1.0/access-token/b2b';

        $body = [
            'grantType' => 'client_credentials',
            'additionalInfo' => ''
        ];
        $body_json = json_encode($body);
        $headers = [
            'Content-Type: application/json',
            'X-TIMESTAMP: ' . $timestamp,
            'X-CLIENT-KEY: ' . $this->client_id,
            'X-SIGNATURE: ' . $this->signature_access_token()
        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        $json = json_decode($response, true);
        return $json['accessToken'];
    }

    public function get_valid_access_token()
    {
        if ($this->access_token && $this->token_timestamp) {
            $current_time = time();
            if (($current_time - $this->token_timestamp) < 900) {
                return $this->access_token;
            }
        }
        $this->access_token = $this->get_access_token();
        $this->token_timestamp = time();
        return $this->access_token;
    }

    private function generateAccessToken($length = 32)
    {
        $randomBytes = openssl_random_pseudo_bytes($length);
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomByte = ord($randomBytes[$i]) % $charactersLength;
            $randomString .= $characters[$randomByte];
        }

        return $randomString;
    }



    public function verifySignatureTest($clientID, $timeStamp, $signature)
    {
        $publicKeyPemPath = 'application/keys/pubkey1.pem';
        if (!file_exists($publicKeyPemPath)) {
            return array('status' => 'error', 'message' => 'File kunci publik tidak ditemukan');
        }
        $publicKeyPem = file_get_contents($publicKeyPemPath);
        $publicKey = openssl_pkey_get_public($publicKeyPem);
        if (!$publicKey) {
            return array('status' => 'error', 'message' => 'Kunci publik tidak valid: ' . openssl_error_string());
        }
        $data = $clientID . "|" . $timeStamp;
        $decodedSignature = base64_decode($signature);
        if ($decodedSignature === false) {
            return array('status' => 'error', 'message' => 'Format Base64 tanda tangan tidak valid');
        }
        $result = openssl_verify($data, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($publicKey);
        if ($result === 1) {
            $accessToken = $this->generateAccessToken(32);
            return array(
                'accessToken' => $accessToken,
                'tokenType' => 'Bearer',
                'expiresIn' => '899'
            );
        } elseif ($result === 0) {
            return array('status' => 'error', 'message' => 'Tanda tangan tidak valid');
        } else {
            return array('status' => 'error', 'message' => 'Kesalahan saat memverifikasi tanda tangan: ' . openssl_error_string());
        }
    }

    public function validateSignature($Authorization, $body, $timeStamp, $signature)
    {

        $Authorization = str_replace('Bearer ', '', $Authorization);
        $httpMethod = 'POST';
        $path = '/bricallback/backend/notifikasi';
        $accessToken = $Authorization;
        $clientSecret = $this->client_secret_push_notif_url;
        $body_json = json_encode($body);
        $bodySHA256 = hash('sha256', $body_json);
        $stringToSign = $httpMethod . ":" . $path . ":" . $accessToken . ":" . $bodySHA256 . ":" . $timeStamp;
        $calculatedSignature = hash_hmac('sha512', $stringToSign, $clientSecret, true);
        $calculatedSignatureBase64 = base64_encode($calculatedSignature);
        if (hash_equals($calculatedSignatureBase64, $signature)) {
            return array('status' => 'success', 'message' => 'Signature valid');
        } else {
            return array('status' => 'error', 'message' => 'Invalid signature', 'result' => $stringToSign);
        }
    }


    public function verifySignature($clientID, $timeStamp, $base64signature)
    {
        $publicKey = file_get_contents($this->publicKeyPath);
        $data = $clientID . "|" . $timeStamp;
        $decodedSignature = base64_decode($base64signature);
        $result = openssl_verify($data, $decodedSignature, $publicKey, OPENSSL_ALGO_SHA256);
        return array(
            'status' => $result === 1 ? 'success' : 'error',
            'message' => $result === 1 ? 'Signature is valid' : ($result === 0 ? 'Signature is invalid' : 'Error verifying signature: ' . openssl_error_string()),
            'result' => $result,
            'body' => array(
                'clientID' => $clientID,
                'timeStamp' => $timeStamp,
                'data' => $data,
                'base64signature' => $base64signature,
                'decodedSignature' => $decodedSignature,
                'publicKey' => $publicKey
            )
        );
    }








    public function get_last_url()
    {
        return $this->last_url;
    }

    public function get_last_headers()
    {
        return $this->last_headers;
    }

    public function get_last_body()
    {
        return $this->last_body;
    }


    public function inquiry_payment_va($partnerServiceId, $customerNo, $virtualAccountNo, $trxDateTime)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_access_token();
        $HttpMethod = 'POST';
        $path = '/api/v1.0/transfer-va/inquiry-intrabank';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateTime' => $trxDateTime,
        ];
        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
        return json_decode($response, true);
    }


    public function payment_va($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $sourceAccountNo, $partnerReferenceNo, $paidAmount, $trxDateTime)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $HttpMethod = 'POST';
        $access_token = $this->get_access_token();
        $path = '/api/v1.0/transfer-va/payment-intrabank';
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
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);

        return json_decode($response, true);
    }




    public function inquiry_virtual_account($partnerServiceId, $customerNo, $virtualAccountNo, $trxId)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_valid_access_token();
        $HttpMethod = 'POST';

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/api/v1.0/transfer-va/inquiry-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId
        ];
        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,
            'Content-Type: application/json'
        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
        return json_decode($response, true);
    }

    public function create_virtual_account($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $totalAmountValue, $totalAmountCurrency, $expiredDate, $trxId, $additionalInfo = null)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_access_token();
        $HttpMethod = 'POST';
        $path = '/api/v1.0/transfer-va/create-va';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'virtualAccountEmail' => 'fahmi4331@gmail.com',
            'virtualAccountPhone' => '082291111892',
            'trxId' => $trxId,
            'totalAmount' => [
                'value' => number_format($totalAmountValue, 2, '.', ''),
                'currency' => $totalAmountCurrency
            ],
            'billDetails' => [
                [
                    'billCode' => '01',
                    'billNo' => '123456789012345678',
                    'billName' => 'Fahmi',
                    'billShortName' => 'Bill A',
                    'billDescription' => [
                        'english' => 'HELLOW',
                        'indonesia' => 'HELLOW'
                    ],
                    'billSubCompany' => '00001',
                    'billAmount' => [
                        'value' => number_format($totalAmountValue, 2, '.', ''),
                        'currency' => $totalAmountCurrency
                    ],
                    'additionalInfo' => []
                ]
            ],
            'freeTexts' => [
                [
                    'english' => 'Free text',
                    'indonesia' => 'Tulisan bebas'
                ]
            ],
            'virtualAccountTrxType' => 'C',
            'feeAmount' => [
                'value' => number_format($totalAmountValue, 2, '.', ''),
                'currency' => $totalAmountCurrency
            ],
            'expiredDate' => $expiredDate,
        ];


        if ($additionalInfo) {
            $body['additionalInfo'] = [
                'description' => $additionalInfo,
                'deviceId' => '12345679237',
                'channel' => 'mobilephone',
            ];
        }

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);

        return json_decode($response, true);
    }


    public function update_status_va($partnerServiceId, $customerNo, $virtualAccountNo, $trxId, $paidStatus)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_access_token();
        $HttpMethod = 'PUT';

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/api/v1.0/transfer-va/update-status';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId,
            'paidStatus' => $paidStatus
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);

        return json_decode($response, true);
    }

    public function update_va($partnerServiceId, $customerNo, $virtualAccountNo, $virtualAccountName, $totalAmountValue, $totalAmountCurrency, $expiredDate, $trxId, $additionalInfo = null)
    {
        $access_token = $this->get_access_token();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $path = '/api/v1.0/transfer-va/update-va';
        $HttpMethod = 'PUT';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'virtualAccountEmail' => 'fahmi4331@gmail.com',
            'virtualAccountPhone' => '082291111892',
            'trxId' => $trxId,
            'totalAmount' => [
                'value' => number_format($totalAmountValue, 2, '.', ''),
                'currency' => $totalAmountCurrency
            ],
            'billDetails' => [
                [
                    'billCode' => '01',
                    'billNo' => '123456789012345678',
                    'billName' => 'Fahmi',
                    'billShortName' => 'Bill A',
                    'billDescription' => [
                        'english' => 'HELLOW',
                        'indonesia' => 'HELLOW'
                    ],
                    'billSubCompany' => '00001',
                    'billAmount' => [
                        'value' => number_format($totalAmountValue, 2, '.', ''),
                        'currency' => $totalAmountCurrency
                    ],
                    'additionalInfo' => []
                ]
            ],
            'freeTexts' => [
                [
                    'english' => 'Free text',
                    'indonesia' => 'Tulisan bebas'
                ]
            ],
            'virtualAccountTrxType' => 'C',
            'feeAmount' => [
                'value' => number_format($totalAmountValue, 2, '.', ''),
                'currency' => $totalAmountCurrency
            ],
            "expiredDate" => "2020-12-31T23:59:59+07:00",
            "additionalInfo" => [
                "deviceId" => "12345679237",
                "channel" => "mobilephone"
            ]
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
        return json_decode($response, true);
    }


    public function delete_va($partnerServiceId, $customerNo, $virtualAccountNo, $trxId)
    {
        $access_token = $this->get_access_token();
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $HttpMethod = 'DELETE';

        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/api/v1.0/transfer-va/delete-va';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxId' => $trxId,
        ];

        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
        return json_decode($response, true);
    }



    public function get_report_va($partnerServiceId, $startDate, $startTime, $endTime)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_access_token();
        $access_token = $this->get_access_token();
        $HttpMethod = 'POST';
        if (isset($access_token['error'])) {
            return ['error' => 'Failed to retrieve access token'];
        }

        $path = '/api/v1.0/transfer-va/report';

        $body = [
            'partnerServiceId' => $partnerServiceId,
            'startDate' => $startDate,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];
        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];
        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
        return json_decode($response, true);
    }


    public function inquiry_status_va($partnerServiceId, $customerNo, $virtualAccountNo, $inquiryRequestId)
    {
        $timezone = new DateTimeZone('Asia/Jakarta');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:sP');
        $access_token = $this->get_access_token();
        $HttpMethod = 'POST';
        $path = '/api/v1.0/transfer-va/status';
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'inquiryRequestId' => $inquiryRequestId
        ];
        $body_json = json_encode($body);
        $external_id = rand(100000000, 999999999);
        $signature = $this->signature_request($access_token, $HttpMethod, $path, $body, $timestamp);
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $access_token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . $this->channel_id,
            'X-EXTERNAL-ID: ' . $external_id,

        ];

        $url = $this->baseUrl . $path;
        $response = $this->send_api_request($url, $HttpMethod, $headers, $body_json);
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

    private function generate_hmac_signature_access_token($timestamp)
    {
        $payload = $timestamp . ':' . $this->client_id . ':' . $this->private_key;
        return hash_hmac('sha512', $payload, $this->private_key);
    }

    private function generate_hmac_signature($path, $method, $timestamp, $token, $body)
    {
        $payload = $method . ':' . $path . ':' . $token . ':' . hash('sha256', $body) . ':' . $timestamp;
        return hash_hmac('sha512', $payload, $this->client_secret);
    }

    private function send_api_request($url, $method = 'POST', $headers = [], $body = null, $callback = null)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $cookieFile = __DIR__ . "/cookie.txt";
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $response = curl_exec($ch);
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'CURL Error: ' . $error];
        }
        curl_close($ch);
        if (is_callable($callback)) {
            return call_user_func($callback, $response);
        }
        return $response;
    }
}
