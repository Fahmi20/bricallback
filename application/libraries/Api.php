<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api
{
    private $baseUrl = 'https://sandbox.partner.api.bri.co.id';

    private $public_key;
    private $public_key_path = '/mnt/data/pubkey.pem';
    private $client_id_push_notif = '8kPf12Bc3HxY47RgQwZ5jT6UvRz1';
    private $client_secret_push_notif = 'Bf45NzPq09XwSa1RtU6Vg8MjYt4R';
    private $token_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/access-token/b2b";
    private $notif_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/transfer-va/notify-payment-intrabank";
    private $public_key_pem = "-----BEGIN PUBLIC KEY-----MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyH96OWkuCmo+VeJAvOOweHhhMZl2VPT9zXv6zr3a3CTwglmDcW4i5fldDzOeL4aco2d+XrPhCscrGKJA4wH1jyVzNcHK+RzsABcKtcqJ4Rira+x02/f554YkXSkxwqqUPtmCMXyr30FCuY3decIu2XsB9WYjpxuUUOdXpOVKzdCrABvZORn7lI2qoHeZ+ECytVYAMw7LDPOfDdo6qnD5Kg+kzVYZBmWC79TW9MaLkLLWNzY7XDe8NBV1KNU+G9/Ktc7S2+fF5jvPc+CWG7CAFHNOkAxyHZ7K1YvA4ghOckQf4EwmxdmDNmEk8ydYVix/nJXiUBY44olhNKr+EKJhYQIDAQAB-----END PUBLIC KEY-----";
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
    private $last_url;
    private $last_headers;
    private $last_body;

    public function __construct()
    {
        $this->private_key = <<<EOD
-----BEGIN RSA PRIVATE KEY-----
MIICWwIBAAKBgQCpLGoAwbDqfyg7KaMaolp+iVxejleZkrizRbB/rXNVHw5D/2wh
N9VRoDfF5nn884Fp5yt7QWNtfNj849pNGWepCT/4bPcZ0ZRghanv96wCMio6xvrC
UWkCwdcokxZfRFZpSzQz3yYhT6FDETb1mKArH23wT1G0EyTDSYorY4huAwIDAQAB
AoGAXOpa8j1vyOu8EfqFbcx7/YG+LOTrMhsGvNfq38VJUhgzgp9YKUp8LE/eMiCr
IYYwrxTbqd+5F1p55zPSI4RvjN+5L4LxaKKyl0MSGETeZstGYqsBy7JxjgbjIYOZ
wG4vglwNra5SG+mBdkzlV2ZBPFjG1qcRq0bhi8JuRhXXeNkCQQDexA7X9AovEC7o
EzW4ZZ7LJO1+dJwuXIuiqSA6YlW+OWdE3Bzp2BWwS/tVqV2v9jI8w5qtANSKr0ka
hiy1W6H9AkEAwmmKSxM8qvppUegJqT9gzw4isXlVx3bjLgzSx7MtKb3mqur012vb
j8MXpvzJ+UmbKX5SYJrdcCweWGTAU+lP/wJADNk6EfKdc8F3MyOIga46znS+zgBj
0bi8xREELtnlICencS1Q7ZvtBFIdmP8/zBpjI2YU0c2udKFPkhwTEBLM8QJAIKGG
TMOV0zzkoJLJzFaO8TH2MMOk2i3iQ8BzQIGaev8c0GNPZTj9SUv9lFGptOXd3UEO
ophbwpAlJ8EBZxQqEQJAeE/dQXB7hICB8A5ZAIFAVWQHJPf/Ahj8VDdpIdVzWK0b
1BV6b19Ki7JbcONQuWbbNr4swlYvj2UFnaGzA43E6g==
-----END RSA PRIVATE KEY-----
EOD;

        $this->CI =& get_instance();
        $this->publicKeyPath = APPPATH . 'keys/pubkey.pem';


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

    private function generateAccessToken($length = 32)
{
    // Hasilkan byte acak
    $randomBytes = openssl_random_pseudo_bytes($length);

    // Definisikan karakter yang diizinkan (alfanumerik: 0-9, a-z, A-Z)
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);

    // Inisialisasi string hasil token
    $randomString = '';

    // Loop untuk menghasilkan token yang terdiri dari karakter-karakter yang diizinkan
    for ($i = 0; $i < $length; $i++) {
        // Ambil byte acak dan map ke karakter yang valid
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
    // Hapus "Bearer " pada Authorization header
    $Authorization = str_replace('Bearer ', '', $Authorization);
    
    // Tentukan HTTP method dan path endpoint
    $httpMethod = 'POST'; // Sesuaikan dengan metode HTTP yang digunakan
    $path = '/bricallback/backend/notifikasi';  // Path yang sesuai dengan endpoint Anda
    $accessToken = $Authorization; // Token akses dari Authorization header
    $clientSecret = $this->client_secret; // Ambil client secret dari konfigurasi Anda
    
    // Konversi body menjadi JSON dan hapus spasi
    $bodyJson = json_encode($body, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $bodyMinified = preg_replace('/\s+/', '', $bodyJson); // Minifikasi body JSON
    
    // Hash body yang telah diminyaki menggunakan SHA-256
    $bodySHA256 = hash('sha256', $bodyMinified);  // Tidak perlu menggunakan hex2bin atau bin2hex
    
    // Buat string untuk di-sign
    $stringToSign = $httpMethod . ":" . $path . ":" . $accessToken . ":" . $bodySHA256 . ":" . $timeStamp;
    
    // Hitung signature dengan HMAC_SHA512 menggunakan clientSecret
    $calculatedSignature = hash_hmac('sha512', $stringToSign, $clientSecret, true);  // Gunakan true untuk hasil dalam bentuk binary
    
    // Ubah signature ke dalam format base64
    $calculatedSignatureBase64 = base64_encode($calculatedSignature);  // Convert to Base64
    
    // Verifikasi signature yang dihitung dengan signature yang diterima
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




    public function get_push_notif_token()
    {
        $path = '/snap/v1.0/access-token/b2b';
        $url = 'https://sandbox.partner.api.bri.co.id' . $path;
        $timestamp = date('Y-m-d\TH:i:s.vP');
        $body = json_encode(['grantType' => 'client_credentials']);
        $stringToSign = $this->client_id . '|' . $timestamp;
        $privateKey = $this->private_key;
        if ($privateKey === false) {
            error_log('Error loading private key.');
            return null;
        }
        $keyResource = openssl_pkey_get_private($privateKey);
        if ($keyResource === false) {
            error_log('Error loading private key: ' . openssl_error_string());
            return null;
        }
        $signature = '';
        $result = openssl_sign($stringToSign, $signature, $keyResource, OPENSSL_ALGO_SHA256);
        openssl_free_key($keyResource);

        if (!$result) {
            error_log('Error signing string: ' . openssl_error_string());
            return null;
        }
        $base64Signature = base64_encode($signature);
        $headers = [
            'X-SIGNATURE: ' . $base64Signature,
            'X-CLIENT-KEY: ' . $this->client_id,
            'X-TIMESTAMP: ' . $timestamp,
            'Content-Type: application/json',
        ];
        $response = $this->send_api_request($url, 'POST', $headers, $body);
        $json = json_decode($response, true);

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            error_log('Error decoding JSON response: ' . json_last_error_msg());
            return null;
        }
        return $json;
    }

    public function get_push_notif_token_test()
    {
        $path = '/snap/v1.0/access-token/b2b';
        $url = 'https://sandbox.partner.api.bri.co.id' . $path;
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $client_ID = $this->client_id_push_notif;
        $publicKeyPath = APPPATH . 'keys/pubkey.pem';
        $publicKey = file_get_contents($publicKeyPath);
        $stringToSign = $client_ID . "|" . $timestamp;
        $signature = base64_encode($stringToSign);
        $result = openssl_verify($stringToSign, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);
        if ($result === 1) {
            echo 'Signature is valid.';
        } elseif ($result === 0) {
            echo 'Signature is invalid.';
        } else {
            echo 'Error verifying signature: ' . openssl_error_string();
        }
        $headers = [
            'X-SIGNATURE: ' . $signature,
            'X-CLIENT-KEY: ' . $this->client_id_push_notif,
            'X-TIMESTAMP: ' . $timestamp,
            'Content-Type: application/json'
        ];

        echo "X-SIGNATURE: $signature\n";
        echo "X-CLIENT-KEY: $client_ID\n";
        echo "X-TIMESTAMP: $timestamp\n";
        echo "Result: $result\n";
        $body = json_encode(['grantType' => 'client_credentials']);
        $response = $this->send_api_request($url, 'POST', $headers, $body);
        $json = json_decode($response, true);
        echo json_encode($json);
    }


    public function send_push_notif($partnerServiceId, $customerNo, $virtualAccountNo, $trxDateTime, $paymentRequestId, $paymentAmount)
    {
        $tokenResponse = $this->get_push_notif_token();
        if (is_array($tokenResponse) && isset($tokenResponse['accessToken'])) {
            $token = $tokenResponse['accessToken'];
        } else {
            throw new Exception("Gagal memperoleh token push notifikasi");
        }

        $path = '/snap/v1.0/transfer-va/notify-payment-intrabank';
        $url = 'https://sandbox.partner.api.bri.co.id' . $path;
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'paymentRequestId' => $paymentRequestId,
            'trxDateTime' => $trxDateTime,
            'additionalInfo' => [
                'idApp' => 'YPGS',
                'passApp' => '354324134',
                'paymentAmount' => $paymentAmount,
                'terminalId' => '9',
                'bankId' => '002'
            ]
        ];
        $bodyJson = json_encode($body);
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $clientID = $this->client_id_push_notif;
        $publicKeyPath = APPPATH . 'keys/pubkey.pem';
        $publicKey = file_get_contents($publicKeyPath);
        $signature = base64_encode($clientID);  // BRI Always base64
        $data = $clientID . "|" . $timestamp;
        $result = openssl_verify($data, base64_decode($signature), $publicKey, OPENSSL_ALGO_SHA256);
        if ($result === 1) {
            echo 'Signature is valid.';
        } elseif ($result === 0) {
            echo 'Signature is invalid.';
        } else {
            echo 'Error verifying signature: ' . openssl_error_string();
        }
        $headers = [
            'Authorization: Bearer ' . $token,
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $result,
            'Content-Type: application/json',
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . 'TRFLA',
            'X-EXTERNAL-ID: ' . rand(100000000, 999999999)
        ];
        $response = $this->send_api_request($url, 'POST', $headers, $bodyJson);
        return json_decode($response, true);
    }



    public function push_notification($partnerServiceId, $customerNo, $virtualAccountNo, $trxDateTime, $paymentRequestId, $paymentAmount)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $path = '/snap/v1.0/transfer-va/notify-payment-intrabank';
        $token = $this->get_valid_access_token();
        $partnerUrl = 'http://127.0.0.1:8000/bricallback/backend/callback';
        $urlTemplate = 'https://sandbox.partner.api.bri.co.id/{partnerUrl}/snap/v1.0/transfer-va/notify-payment-intrabank';
        $url = str_replace('{partnerUrl}', $partnerUrl, $urlTemplate);

        $body = array(
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'trxDateTime' => $trxDateTime,
            'paymentRequestId' => $paymentRequestId,
            'paymentAmount' => $paymentAmount,
            'idApp' => 'YPGS',
            'passApp' => '12345',
            'terminalId' => '9',
            'bankId' => '002',
            'partnerUrl' => $partnerUrl,
            'clientSecret' => $this->client_secret,
            'token' => $token
        );

        $body_json = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = array(
            'Authorization: Bearer ' . $token,
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'Content-Type: application/json',
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . 'TRFLA',
            'X-EXTERNAL-ID: ' . $external_id
        );

        log_message('info', 'Sending request to: ' . $url);
        log_message('info', 'Request body: ' . $body_json);
        echo 'URL: ' . $url . '<br>';
        echo 'Headers: ' . json_encode($headers) . '<br>';
        echo 'Body: ' . $body_json . '<br>';

        // Kirim request
        $response = $this->send_api_request($url, 'POST', $headers, $body_json);

        return json_decode($response, true);
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


    public function inquiry_payment_va_briva(
        $partnerServiceId,
        $customerNo,
        $virtualAccountNo,
        $amount,
        $inquiryRequestId
    ) {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z');
        $path = '/snap/v1.0/transfer-va/inquiry';
        $token = $this->get_valid_access_token();
        $partnerUrl = 'http://103.167.35.206:8000/bricallback/backend/inquiry_payment_va_callback';
        $endTime = (new DateTime('now', new DateTimeZone('Asia/Jakarta')))
            ->add(new DateInterval('P1D'))
            ->format('Y-m-d\TH:i:sP');
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'amount' => (float) $amount,
            'currency' => 'IDR',
            'trxDateInit' => '',
            'channelCode' => '',
            'sourceBankCode' => '002',
            'passApp' => 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh',
            'inquiryRequestId' => $inquiryRequestId,
            'idApp' => 'YPGS',
            'partnerUrl' => $partnerUrl,
            'clientSecret' => $this->client_secret,
            'token' => $token,
            'endTime' => $endTime

        ];
        $body_json = json_encode($body, JSON_UNESCAPED_SLASHES);
        $signature = $this->generate_hmac_signature($path, 'POST', $timestamp, $token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = [
            'Authorization: Bearer ' . $token,
            'X-SIGNATURE: ' . $signature,
            'X-TIMESTAMP: ' . $timestamp,
            'X-PARTNER-ID: ' . $this->partner_id,
            'CHANNEL-ID: ' . '00009',
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
