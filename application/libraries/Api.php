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
    private $public_key;

    private $token_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/access-token/b2b"; // URL token BRI
    private $notif_url = "https://sandbox.partner.api.bri.co.id/snap/v1.0/transfer-va/notify-payment-intrabank"; // URL notifikasi BRI

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

    $this->public_key = "-----BEGIN PUBLIC KEY-----\n" .
                            chunk_split("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyH96OWkuCmo+VeJAvOOweHhhMZl2VPT9zXv6zr3a3CTwglmDcW4i5fldDzOeL4aco2d+XrPhCscrGKJA4wH1jyVzNcHK+RzsABcKtcqJ4Rira+x02/f554YkXSkxwqqUPtmCMXyr30FCuY3decIu2XsB9WYjpxuUUOdXpOVKzdCrABvZORn7lI2qoHeZ+ECytVYAMw7LDPOfDdo6qnD5Kg+kzVYZBmWC79TW9MaLkLLWNzY7XDe8NBV1KNU+G9/Ktc7S2+fF5jvPc+CWG7CAFHNOkAxyHZ7K1YvA4ghOckQf4EwmxdmDNmEk8ydYVix/nJXiUBY44olhNKr+EKJhYQIDAQAB", 64) .
                            "-----END PUBLIC KEY-----";

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

    // Verifikasi tanda tangan menggunakan pubkey.pem
    public function verify_bri_signature($data, $signature)
    {
        // Path ke file pubkey.pem
        $publicKeyPath = '/mnt/data/pubkey.pem';
        $publicKey = file_get_contents($publicKeyPath);

        if (!$publicKey) {
            throw new Exception('Gagal membaca file public key.');
        }

        // Konversi public key menjadi resource
        $publicKeyId = openssl_get_publickey($publicKey);

        if (!$publicKeyId) {
            throw new Exception('Gagal memuat kunci publik untuk verifikasi.');
        }

        // Decode signature dari base64
        $signatureDecoded = base64_decode($signature);

        // Verifikasi signature menggunakan public key
        $isValid = openssl_verify($data, $signatureDecoded, $publicKeyId, OPENSSL_ALGO_SHA256);

        // Bebaskan resource public key
        openssl_free_key($publicKeyId);

        return $isValid === 1;
    }

    public function handle_bri_notification($notification)
    {
        // Ambil data dan signature dari notifikasi
        $data = json_encode($notification['data']); // Sesuaikan dengan struktur data notifikasi
        $signature = $notification['signature'];

        // Verifikasi signature notifikasi
        if ($this->verify_bri_signature($data, $signature)) {
            // Signature valid, lanjutkan ke get_push_notif_token dan send_push_notif
            try {
                $token = $this->get_push_notif_token();

                // Contoh pemanggilan send_push_notif
                $this->send_push_notif(
                    $notification['data']['partnerServiceId'],
                    $notification['data']['customerNo'],
                    $notification['data']['virtualAccountNo'],
                    $notification['data']['trxDateTime'],
                    $notification['data']['paymentRequestId'],
                    $notification['data']['paymentAmount']
                );

                return true; // Notifikasi berhasil diproses dan dikirim

            } catch (Exception $e) {
                throw new Exception("Error dalam pengiriman notifikasi: " . $e->getMessage());
            }
        } else {
            // Signature tidak valid
            throw new Exception('Signature notifikasi BRI tidak valid.');
        }
    }

    public function get_push_notif_token()
    {
        $url = $this->token_url;
        $timezone = new DateTimeZone('+07:00');
        $datetime = new DateTime('now', $timezone);
        $timestamp = $datetime->format('Y-m-d\TH:i:s.vP');
        $body = json_encode([
            'grantType' => 'client_credentials'
        ]);
        $stringToSign = $this->client_id . '|' . $timestamp;
        $privateKey = $this->client_secret;
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

    public function send_push_notif($partnerServiceId, $customerNo, $virtualAccountNo, $trxDateTime, $paymentRequestId, $paymentAmount)
    {
        $timestamp = gmdate('Y-m-d\TH:i:s\Z', time());
        $token = $this->get_push_notif_token();
        $url = $this->notif_url;
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
        $body_json = json_encode($body);
        $signature = $this->generate_hmac_signature($url, 'POST', $timestamp, $token, $body_json);
        $external_id = rand(100000000, 999999999);
        $headers = array(
            'Authorization: Bearer ' . $token,
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'Content-Type: application/json',
            'X-PARTNER-ID: ' . $this->client_id,
            'CHANNEL-ID: ' . 'TRFLA',
            'X-EXTERNAL-ID: ' . $external_id
        );

        $response = $this->send_api_request($url, 'POST', $headers, $body_json);
        return json_decode($response, true);
    }

    public function send_api_request_push_notif($url, $method, $headers, $body, $callback = null)
    {
        $ch = curl_init();
        $content_length = strlen($body);
        $headers[] = "Content-Length: " . $content_length;
        $headers[] = "Accept-Encoding: gzip, deflate";
        $headers[] = "Cache-Control: max-age=0";
        $headers[] = "Connection: keep-alive";
        $headers[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);  // Set method POST
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);    // Set headers
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);       // Set body JSON
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);    // Kembalikan respons sebagai string
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);    // Ikuti redirect (--location)
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);   // Nonaktifkan verifikasi SSL (untuk testing sandbox)
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);   // Nonaktifkan verifikasi host (untuk testing sandbox)

        // Tambahan untuk User-Agent
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        // Eksekusi cURL
        $response = curl_exec($ch);

        // Cek error cURL
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            log_message('error', 'cURL error: ' . $error_msg);
            curl_close($ch);
            return false;
        }

        // Tutup cURL
        curl_close($ch);

        // Panggil callback jika diberikan
        if ($callback && is_callable($callback)) {
            call_user_func($callback, json_decode($response, true));
        }

        return $response;
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
            ->add(new DateInterval('P1D'))  // Tambahkan 1 hari
            ->format('Y-m-d\TH:i:sP');  // Format ISO-8601 dengan offset zona waktu
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
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Tambahan: Menangani cookie
        $cookieFile = __DIR__ . "/cookie.txt";
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

        // Tambahan: Pengaturan User Agent
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

        // Tambahan: Timeout agar permintaan tidak menggantung
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);  // 30 detik timeout
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // 10 detik timeout koneksi

        // Tambahan: Opsi untuk mengikuti redirect jika ada
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);  // Maksimal 5 redirect

        // Metode HTTP
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

        // Tambahan: Verifikasi SSL dinonaktifkan untuk pengujian sandbox
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        // Tambahan: Cek respons dan error
        if ($response === false) {
            $error = curl_error($ch);
            curl_close($ch);
            return ['error' => 'CURL Error: ' . $error];
        }

        curl_close($ch);

        // Pemanggilan callback jika tersedia
        if (is_callable($callback)) {
            return call_user_func($callback, $response);
        }

        return $response;
    }



}
