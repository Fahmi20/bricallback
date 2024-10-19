<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class SnapOAuth {
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;
    protected $token;

    public function __construct($config = array()) {
        // Menggunakan nilai default jika config tidak diberikan
        $this->clientId = isset($config['clientId']) ? $config['clientId'] : 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh';
        $this->clientSecret = isset($config['clientSecret']) ? $config['clientSecret'] : 'MNfGscq4w6XUmAp3';
        $this->baseUrl = isset($config['baseUrl']) ? $config['baseUrl'] : 'https://sandbox.partner.api.bri.co.id';
        $this->token = null;
    }

    // Fungsi untuk mendapatkan Access Token
    public function getAccessToken() {
        // URL endpoint untuk mendapatkan token
        $url = $this->baseUrl . '/oauth/client_credential/accesstoken?grant_type=client_credentials';

        // Header tidak memerlukan Basic Auth; hanya Content-Type yang diperlukan
        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        // Data yang akan dikirim dalam body
        $data = http_build_query([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials'
        ]);

        // Kirim permintaan ke API
        $response = $this->sendRequest('POST', $url, $data, $headers);

        // Periksa apakah respons berisi access_token
        if ($response && isset($response->access_token)) {
            $this->token = $response->access_token;
            return $this->token;
        } else {
            // Jika gagal, kembalikan false
            return false;
        }
    }

    // Fungsi untuk mengirim notifikasi pembayaran ke Virtual Account (Intrabank)
    public function notifyPaymentIntrabank($data) {
        // URL endpoint notifikasi pembayaran
        $url = $this->baseUrl . '/snap/v1.0/transfer-va/notify-payment-intrabank';

        // Header yang dibutuhkan
        $timestamp = gmdate("Y-m-d\TH:i:s\Z"); // X-TIMESTAMP
        $signature = hash_hmac('sha512', $timestamp . $this->clientId, $this->clientSecret); // X-SIGNATURE

        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),  // Menggunakan token yang didapatkan
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'X-PARTNER-ID: YGSDev',
            'Content-Type: application/json'
        ];

        // Data yang akan dikirim dalam format JSON
        $jsonData = json_encode($data);

        // Mengirimkan request menggunakan cURL
        $response = $this->sendRequest('POST', $url, $jsonData, $headers);

        return $response;
    }

    // Fungsi tambahan baru untuk validasi status transaksi
    public function validateTransactionStatus($transactionId) {
        // URL endpoint untuk validasi status transaksi
        $url = $this->baseUrl . '/snap/v1.0/transfer-va/validate-transaction-status/' . $transactionId;

        // Header yang dibutuhkan
        $timestamp = gmdate("Y-m-d\TH:i:s\Z"); // X-TIMESTAMP
        $signature = hash_hmac('sha512', $timestamp . $this->clientId, $this->clientSecret); // X-SIGNATURE

        $headers = [
            'Authorization: Bearer ' . $this->getAccessToken(),  // Menggunakan token yang didapatkan
            'X-TIMESTAMP: ' . $timestamp,
            'X-SIGNATURE: ' . $signature,
            'X-PARTNER-ID: your_partner_id',
            'Content-Type: application/json'
        ];

        // Mengirimkan request menggunakan cURL
        $response = $this->sendRequest('GET', $url, null, $headers);

        return $response;
    }

    // Fungsi untuk mengirim request HTTP menggunakan cURL
    private function sendRequest($method, $url, $data = null, $headers = array()) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Eksekusi cURL
        $result = curl_exec($ch);
        curl_close($ch);

        // Mengembalikan hasil dalam bentuk JSON yang sudah di-decode
        return json_decode($result);
    }
}
