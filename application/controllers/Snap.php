<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Snap extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('SnapOAuth');
    }

    public function send_notification() {
        // Data yang akan dikirim ke API
        $data = [
            'partnerServiceId' => '___77777',
            'customerNo' => '08577508881',
            'virtualAccountNo' => '___7777708577508881',
            'trxDateTime' => '2023-12-04T08:34:00+07:00',
            'paymentRequestId' => '2027912345671234567',
            'additionalInfo' => [
                'paymentAmount' => '650000',
                'terminalId' => '1',
                'bankId' => '002'
            ]
        ];

        // Mengirim notifikasi menggunakan library SnapOAuth
        $response = $this->snapoauth->notifyPaymentIntrabank($data);

        // Menampilkan response
        echo json_encode($response);
    }

    // Fungsi baru untuk validasi status transaksi
    public function validate_transaction($transactionId) {
        $response = $this->snapoauth->validateTransactionStatus($transactionId);

        // Menampilkan hasil validasi
        echo json_encode($response);
    }
}
