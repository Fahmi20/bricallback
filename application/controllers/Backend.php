<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backend extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('api');
        $this->load->library('hit');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VirtualAccountModel');
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_access_token()
    {
        $access_token = $this->api->get_access_token();
        echo json_encode($access_token);
    }

    public function get_access_token_push_notif()
    {
        $this->api->get_push_notif_token();
    }

    public function trigger_token_test()
    {
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: X-SIGNATURE, X-CLIENT-KEY, X-TIMESTAMP, Content-Type");
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
        $signature = $this->input->get_request_header('X-SIGNATURE', TRUE);
        $clientID = $this->input->get_request_header('X-CLIENT-KEY', TRUE);
        $timeStamp = $this->input->get_request_header('X-TIMESTAMP', TRUE);
        if (empty($signature)) {
            echo json_encode(array('status' => 'error', 'message' => 'Signature not provided'));
            return;
        }
        if (empty($timeStamp)) {
            echo json_encode(array('status' => 'error', 'message' => 'Timestamp not provided'));
            return;
        }
        if (empty($clientID)) {
            echo json_encode(array('status' => 'error', 'message' => 'Client not provided'));
            return;
        }
        $response = $this->api->verifySignatureTest($signature, $timeStamp, $clientID);
        echo json_encode($response);
    }




    public function trigger_token()
{
    // Set CORS headers
    header("Access-Control-Allow-Origin: https://apidevportal.aspi-indonesia.or.id");
    header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
    header("Access-Control-Allow-Headers: X-TIMESTAMP, X-CLIENT-KEY, X-CLIENT-SECRET, Content-Type, X-SIGNATURE, Accept, Authorization, Authorization-Customer, ORIGIN, X-PARTNER-ID, X-EXTERNAL-ID, X-IP-ADDRESS, X-DEVICE-ID, CHANNEL-ID, X-LATITUDE, X-LONGITUDE");
    header("Access-Control-Allow-Credentials: true");

    // Handle preflight request (OPTIONS)
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    try {
        // Only handle POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(405) // Method Not Allowed
                ->set_output(json_encode([
                    'status' => 'error',
                    'message' => 'Method not allowed, POST required.'
                ]));
            return;
        }

        // Get the necessary headers
        $signature = $this->input->get_request_header('X-SIGNATURE', TRUE);
        $clientID = $this->input->get_request_header('X-PARTNER-ID', TRUE);
        $timeStamp = $this->input->get_request_header('X-TIMESTAMP', TRUE);
        // Verify the signature
        $verificationResult = $this->api->verifySignatureTest($clientID, $timeStamp, $signature);

        // Check if the verification was successful and accessToken is available
        if (isset($verificationResult['accessToken'])) {
            $accessToken = $verificationResult['accessToken'];
            $expiresIn = $verificationResult['expiresIn'];

            // Save access token to the database
            $this->load->model('VirtualAccountModel');
            $this->VirtualAccountModel->saveAccessToken($clientID, $accessToken, $expiresIn);

            // Return successful response
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode([
                    'responseCode' => '2003400',
                    'responseMessage' => 'Successful',
                    'tokenType' => 'Bearer',
                    'accessToken' => $accessToken,
                    'expiresIn' => $expiresIn
                ], JSON_PRETTY_PRINT));
        } else {
            // Handle verification failure
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400) // Bad Request
                ->set_output(json_encode( $verificationResult, JSON_PRETTY_PRINT));
        }

    } catch (Exception $e) {
        // Handle any exception that occurs during processing
        log_message('error', 'Error during trigger_token: ' . $e->getMessage());
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(500) // Internal Server Error
            ->set_output(json_encode([
                'status' => 'error',
                'message' => 'Internal server error occurred.'
            ], JSON_PRETTY_PRINT));
    }
}


public function notifikasi()
{
    // Set headers untuk CORS
    header("Access-Control-Allow-Origin: https://apidevportal.aspi-indonesia.or.id");
    header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
    header("Access-Control-Allow-Headers: X-TIMESTAMP, X-CLIENT-KEY, X-CLIENT-SECRET, Content-Type, X-SIGNATURE, Accept, Authorization, Authorization-Customer, ORIGIN, X-PARTNER-ID, X-EXTERNAL-ID, X-IP-ADDRESS, X-DEVICE-ID, CHANNEL-ID, X-LATITUDE, X-LONGITUDE");
    header("Access-Control-Allow-Credentials: true");

    // Jika request adalah preflight (OPTIONS request), langsung return
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }

    try {
        // Validasi method POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }

        // Validasi dan proses header yang dibutuhkan
        $requiredHeaders = ['Authorization', 'X-SIGNATURE', 'X-TIMESTAMP', 'X-PARTNER-ID', 'CHANNEL-ID', 'X-EXTERNAL-ID', 'Content-Type'];
        foreach ($requiredHeaders as $header) {
            $headerValue = $this->input->get_request_header($header, TRUE);
            if (empty($headerValue)) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'responseCode' => '400',
                        'responseMessage' => "Missing header: $header"
                    ]));
                return;
            }
        }

        // Memproses data body dari request
        $body = file_get_contents('php://input');
        $requestData = json_decode($body, true);

        // Verifikasi access token
        $Authorization = $this->input->get_request_header('Authorization', TRUE);
        $accessToken = substr($Authorization, 7); // Mengambil token tanpa "Bearer "
        $this->load->model('VirtualAccountModel');
        $storedToken = $this->VirtualAccountModel->getAccessTokenByToken($accessToken);
        if (!$storedToken) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode([
                    'responseCode' => '4017301',
                    'responseMessage' => 'Invalid Token (B2B)'
                ]));
            return;
        }

        // Lakukan verifikasi signature
        $signature = $this->input->get_request_header('X-SIGNATURE', TRUE);
        $timeStamp = $this->input->get_request_header('X-TIMESTAMP', TRUE);
        $verificationResult = $this->api->validateSignature($Authorization, $requestData, $timeStamp, $signature);
        
        if ($verificationResult['status'] === 'success') {
            if (empty($requestData['partnerServiceId'])) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode([
                        'responseCode' => '400',
                        'responseMessage' => 'Missing partnerServiceId in request body'
                    ]));
                return;
            }

            // Simpan data pembayaran
            $saveResult = $this->VirtualAccountModel->savePaymentData($requestData);

            if ($saveResult) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode([
                        'responseCode' => '2003400',
                        'responseMessage' => 'Successful',
                        'virtualAccountData' => [
                            'partnerServiceId' => $requestData['partnerServiceId'],
                            'customerNo' => $requestData['customerNo'],
                            'virtualAccountNo' => $requestData['virtualAccountNo'],
                            'paymentRequestId' => $requestData['paymentRequestId'],
                            'trxDateTime' => $requestData['trxDateTime'],
                            'paymentStatus' => 'Success'
                        ]
                    ]));
            } else {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode([
                        'responseCode' => '500',
                        'responseMessage' => 'Failed to save payment data'
                    ]));
            }
        } else {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode([
                    'responseCode' => '400',
                    'responseMessage' => $verificationResult['message'],
                    'result' => $verificationResult['result']
                ]));
        }
    } catch (Exception $e) {
        log_message('error', 'Error during signature validation: ' . $e->getMessage());
        $this->output
            ->set_content_type('application/json')
            ->set_status_header(500)
            ->set_output(json_encode([
                'responseCode' => '500',
                'responseMessage' => 'Internal server error'
            ]));
    }
}







    public function inquiry_payment_va_briva_controller()
    {
        $partnerServiceId = '03636';
        $customerNo = '444333';
        $partnerServiceId = str_pad($partnerServiceId, 8, '0', STR_PAD_LEFT);  // "    77777"
        $virtualAccountNo = $partnerServiceId . $customerNo;
        if (strlen($virtualAccountNo) > 28) {
            throw new Exception("virtualAccountNo terlalu panjang. Maksimal 28 karakter.");
        }
        var_dump($virtualAccountNo);
        echo "Panjang virtualAccountNo: " . strlen($virtualAccountNo);
        $postData = [
            'partnerServiceId' => trim($partnerServiceId),
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'amount' => '12345.00',
            'currency' => 'IDR',
            'inquiryRequestId' => uniqid('inq_')
        ];
        $response = $this->api->inquiry_payment_va_briva(
            $postData['partnerServiceId'],
            $postData['customerNo'],
            $postData['virtualAccountNo'],
            $postData['amount'],
            $postData['inquiryRequestId']
        );
        echo json_encode($response);
    }

    public function get_inquiry_payment_va_briva_controller()
    {
        $partnerServiceId = $this->input->get('partnerServiceId');
        $customerNo = $this->input->get('customerNo');
        $virtualAccountNo = $this->input->get('virtualAccountNo');
        $amount = $this->input->get('amount');
        $trxDateInit = $this->input->get('trxDateInit');
        $inquiryRequestId = $this->input->get('inquiryRequestId');
        $channelCode = '9'; // Kode channel tetap
        $sourceBankCode = '002'; // Kode bank, misalnya BRI
        $passApp = 'G6bDFAAbwTUhqhMGa9qOsydLGBexH6bh';
        $idApp = 'YPGS';
        $partnerUrl = 'http://103.167.35.206:8000';

        // Menyusun body request
        $body = [
            'partnerServiceId' => $partnerServiceId,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'amount' => [
                'value' => (float) $amount, // Memastikan amount dalam tipe float
                'currency' => 'IDR' // Menetapkan mata uang
            ],
            'trxDateInit' => $trxDateInit,
            'channelCode' => $channelCode,
            'sourceBankCode' => $sourceBankCode,
            'passApp' => $passApp,
            'inquiryRequestId' => $inquiryRequestId,
            'idApp' => $idApp,
            'partnerUrl' => $partnerUrl
        ];
        return $body;
    }

    public function payment_va_briva_controller()
    {

        $postData = [
            'partnerServiceId' => '   03636',
            'customerNo' => '444444',
            'virtualAccountNo' => '   03636444444',
            'trxDateTime' => '2024-10-20T23:05:07+07:00',
            'paymentRequestId' => '1729353906',
            'additionalInfo' => [
                'paymentAmount' => '1234.00'
            ]
        ];

        try {
            $response = $this->api->payment_va_briva(
                $postData['partnerServiceId'],
                $postData['customerNo'],
                $postData['virtualAccountNo'],
                $postData['trxDateTime'],
                $postData['paymentRequestId'],
                $postData['additionalInfo']
            );

            header('Content-Type: application/json');
            echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function inquiry_payment()
    {
        $virtualAccounts = $this->VirtualAccountModel->get_all_payment_virtual_accounts();
        $responses = [];

        if ($virtualAccounts) {
            foreach ($virtualAccounts as $virtualAccount) {
                $data = [
                    'partnerServiceId' => $virtualAccount->partner_service_id,
                    'customerNo' => $virtualAccount->customer_no,
                    'virtualAccountNo' => $virtualAccount->virtual_account_no
                ];

                $response = $this->api->inquiry_payment_va(
                    $data['partnerServiceId'],
                    $data['customerNo'],
                    $data['virtualAccountNo']
                );
                if ($response['responseCode'] === "2003200") {
                    $responses[] = [
                        'customerNo' => $response['virtualAccountData']['customerNo'],
                        'virtualAccountNo' => $response['virtualAccountData']['virtualAccountNo'],
                        'virtualAccountName' => $response['virtualAccountData']['virtualAccountName'],
                        'totalAmount' => $response['virtualAccountData']['totalAmount'],
                        'additionalInfo' => $response['virtualAccountData']['additionalInfo']
                    ];
                }
            }
        }
        echo json_encode(['data' => $responses]);
    }

    public function inquiry_paymentVA()
    {
        $partnerServiceId = isset($_POST['partnerServiceId']);
        $customerNo = isset($_POST['customerNo']);
        $virtualAccountNo = isset($_POST['virtualAccountNo']);
        $response = $this->api->inquiry_payment_va(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo
        );

        if (isset($response['responseCode']) && $response['responseCode'] == '2003200') {
            $virtualAccountData = isset($response['virtualAccountData']);
            $dataToSave = array(
                'partnerServiceId' => isset($virtualAccountData['partnerServiceId']),
                'customerNo' => isset($virtualAccountData['customerNo']),
                'virtualAccountNo' => isset($virtualAccountData['virtualAccountNo']),
                'virtualAccountName' => isset($virtualAccountData['virtualAccountName']),
                'partnerReferenceNo' => isset($virtualAccountData['partnerReferenceNo']),
                'paidAmount' => array(
                    'value' => isset($virtualAccountData['totalAmount']['value']),
                    'currency' => isset($virtualAccountData['totalAmount']['currency']),
                ),
                'trxDateTime' => isset($virtualAccountData['trxDateTime']),
                'paymentRequestId' => isset($virtualAccountData['paymentRequestId']),
            );
            $this->VirtualAccountModel->save_payment($dataToSave);
            echo json_encode(array(
                "status" => true,
                "message" => "Inquiry berhasil.",
                "data" => $virtualAccountData
            ));
        } else {
            echo json_encode(array(
                "status" => false,
                "message" => "Inquiry gagal.",
                "data" => array()
            ));
        }
    }


    public function process_payment_transfer_to_va()
    {
        $partnerServiceId = '00000';//03636
        $customerNo = '34071';
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $inquiryResponse = $this->api->inquiry_payment_va($partnerServiceIdWithSpaces, $customerNo, $virtualAccountNo);
        if (isset($inquiryResponse['responseCode']) && $inquiryResponse['responseCode'] == '2003200') {
            $virtualAccountData = $inquiryResponse['virtualAccountData'];
            $virtualAccountName = $virtualAccountData['virtualAccountName'];
            $totalAmount = $virtualAccountData['totalAmount'];
            $paidAmountValue = $totalAmount['value'];
            $paidAmountCurrency = $totalAmount['currency'];
            $sourceAccountNo = '123';
            $partnerReferenceNo = $virtualAccountData['partnerReferenceNo'];
            $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
            $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');  // Format sesuai ISO-8601
            $paidAmount = [
                'value' => $paidAmountValue,
                'currency' => $paidAmountCurrency
            ];
            $paymentResponse = $this->api->payment_va(
                $partnerServiceIdWithSpaces,
                $customerNo,
                $virtualAccountNo,
                $virtualAccountName,
                $sourceAccountNo,
                $partnerReferenceNo,
                $paidAmount,
                $trxDateTimeFormatted
            );

            if (isset($paymentResponse['responseCode']) && $paymentResponse['responseCode'] == '2003300') {
                $this->VirtualAccountModel->save_payment($paymentResponse['virtualAccountData']);
                $output = [
                    'status' => true,
                    'message' => 'Pembayaran berhasil',
                    'data' => $paymentResponse
                ];
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($output));
            } else {
                $output = [
                    'status' => false,
                    'message' => 'Gagal melakukan pembayaran',
                    'data' => $paymentResponse
                ];
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode($output));
            }
        } else {
            $output = [
                'status' => false,
                'message' => 'Inquiry VA gagal',
                'data' => $inquiryResponse
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode($output));
        }
    }

    public function test_inquiry_payment()
    {
        $partnerServiceId = '   03636';
        $customerNo = '565656';
        $virtualAccountNo = '   03636565656';
        $response = $this->api->inquiry_payment_va(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo
        );
        echo json_encode($response);
    }

    public function test_payment_va()
    {
        $partnerServiceId = '   22084';
        $customerNo = '444492';
        $virtualAccountNo = '   22084444492';
        $virtualAccountName = 'Fahmi';
        $sourceAccountNo = '123456789012345';
        $partnerReferenceNo = '173856548336311';
        $paidAmountValue = '500.00';
        $paidAmountCurrency = 'IDR';
        $paidAmount = [
            'value' => $paidAmountValue,
            'currency' => $paidAmountCurrency
        ];
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');

        $response = $this->api->payment_va(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo,
            $virtualAccountName,
            $sourceAccountNo,
            $partnerReferenceNo,
            $paidAmount,
            $trxDateTimeFormatted
        );

        // Output the result

        echo json_encode($response);

    }

    public function create_virtual_account_manual_sisfo()
    {
        
        $partnerServiceId = $this->input->post('partnerServiceId');
        $customerNo = $this->input->post('customerNo');
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $virtualAccountName = $this->input->post('virtualAccountName');
        $totalAmount = $this->input->post('totalAmount');
        $totalAmountCurrency = 'IDR';
        $expiredDateInput = $this->input->post('expiredDate');
        $trxId = $this->input->post('trxId');
        $additionalInfo = $this->input->post('additionalInfo');
        $trx_nim = $this->input->post('trx_nim');
        $Jumlah = $this->input->post('Jumlah');

        if ($Jumlah > 6) {
            $Jumlah = 6;
        }
        $expiredDate = new DateTime($expiredDateInput, new DateTimeZone('Asia/Jakarta'));
        $expiredDate->modify('+7 years');
        $expiredDateWithTimezone = $expiredDate->format('Y-m-d\TH:i:sP');
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');
        $responses = [];
        $errors = [];

        for ($i = 1; $i <= $Jumlah; $i++) {
            $partnerReferenceNo = $this->generate_unique_payment_id() . $i;
            $data = [
                'partnerServiceId' => $partnerServiceIdWithSpaces,
                'customerNo' => $customerNo,
                'virtualAccountNo' => $virtualAccountNo,
                'virtualAccountName' => $virtualAccountName,
                'totalAmount' => $totalAmount,
                'totalAmountCurrency' => $totalAmountCurrency,
                'expiredDate' => $expiredDateWithTimezone,
                'trxId' => $trxId . '-' . $i,
                'additionalInfo' => $additionalInfo,
                'partnerReferenceNo' => $partnerReferenceNo,
                'trxDateTime' => $trxDateTimeFormatted,
                'trx_nim' => $trx_nim,
                'partNumber' => $i,
                'resend' => 0
            ];

            try {
                $response = $this->api->create_virtual_account(
                    $data['partnerServiceId'],
                    $data['customerNo'],
                    $data['virtualAccountNo'],
                    $data['virtualAccountName'],
                    $data['totalAmount'],
                    $data['totalAmountCurrency'],
                    $data['expiredDate'],
                    $data['trxId'],
                    $data['additionalInfo']
                );

                if ($response['responseCode'] === "2002700") {
                    $data['Status'] = 'success';
                    $data['Deskripsi_Va'] = 'Virtual Account berhasil dibuat';
                } else {
                    $data['Status'] = 'error';
                    $data['Deskripsi_Va'] = $response['responseMessage'];
                    $data['resend'] = 1;
                    $errors[] = [
                        'virtualAccountNo' => $virtualAccountNo,
                        'error' => $response['responseMessage']
                    ];
                }
            } catch (Exception $e) {
                $data['Status'] = 'error';
                $data['Deskripsi_Va'] = 'API call gagal: ' . $e->getMessage();
                $data['resend'] = 1;
                $errors[] = [
                    'virtualAccountNo' => $virtualAccountNo,
                    'error' => 'API call gagal: ' . $e->getMessage()
                ];
                $response = null;
            }

            // Simpan ke database
            $save_status = $this->VirtualAccountModel->save_virtual_account($data);

            if ($save_status) {
                $responses[] = [
                    'virtualAccountNo' => $virtualAccountNo,
                    'status' => 'success',
                    'data' => isset($response) ? $response : null
                ];
            } else {
                $errors[] = [
                    'virtualAccountNo' => $virtualAccountNo,
                    'error' => 'Gagal menyimpan ke database'
                ];
            }
        }

        if (!empty($errors)) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Beberapa Virtual Account gagal dibuat atau disimpan',
                'errors' => $errors,
                'success_responses' => $responses
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Semua Virtual Account berhasil dibuat dan disimpan',
                'responses' => $responses
            ]);
        }
    }

    public function resend_failed_virtual_accounts()
    {
        // Ambil semua entri virtual account dengan resend = 1 dari database
        $failed_accounts = $this->VirtualAccountModel->get_failed_virtual_accounts();

        $responses = [];
        $errors = [];

        foreach ($failed_accounts as $account) {
            // Data virtual account yang gagal
            $data = [
                'partnerServiceId' => $account['partnerServiceId'],
                'customerNo' => $account['customerNo'],
                'virtualAccountNo' => $account['virtualAccountNo'],
                'virtualAccountName' => $account['virtualAccountName'],
                'totalAmount' => $account['totalAmount'],
                'totalAmountCurrency' => $account['totalAmountCurrency'],
                'expiredDate' => $account['expiredDate'],
                'trxId' => $account['trxId'],
                'additionalInfo' => $account['additionalInfo']
            ];

            try {
                // Kirim ulang ke API
                $response = $this->api->create_virtual_account(
                    $data['partnerServiceId'],
                    $data['customerNo'],
                    $data['virtualAccountNo'],
                    $data['virtualAccountName'],
                    $data['totalAmount'],
                    $data['totalAmountCurrency'],
                    $data['expiredDate'],
                    $data['trxId'],
                    $data['additionalInfo']
                );

                if ($response['responseCode'] === "2002700") {
                    // Update status di database sebagai berhasil
                    $update_data = [
                        'Status' => 'success',
                        'Deskripsi_Va' => 'Virtual Account berhasil dibuat pada percobaan ulang',
                        'resend' => 0 // Hapus tanda untuk dikirim ulang
                    ];
                    $this->VirtualAccountModel->update_virtual_account_status($account['virtualAccountNo'], $update_data);
                    $responses[] = [
                        'virtualAccountNo' => $data['virtualAccountNo'],
                        'status' => 'success',
                        'message' => 'Dikirim ulang dan berhasil'
                    ];
                } else {
                    // Update dengan pesan error terbaru
                    $update_data = [
                        'Status' => 'error',
                        'Deskripsi_Va' => $response['responseMessage']
                    ];
                    $this->VirtualAccountModel->update_virtual_account_status($account['virtualAccountNo'], $update_data);
                    $errors[] = [
                        'virtualAccountNo' => $data['virtualAccountNo'],
                        'error' => $response['responseMessage']
                    ];
                }
            } catch (Exception $e) {
                // Update dengan pesan error jika terjadi exception
                $update_data = [
                    'Status' => 'error',
                    'Deskripsi_Va' => 'API call gagal pada percobaan ulang: ' . $e->getMessage()
                ];
                $this->VirtualAccountModel->update_virtual_account_status($account['virtualAccountNo'], $update_data);
                $errors[] = [
                    'virtualAccountNo' => $data['virtualAccountNo'],
                    'error' => 'API call gagal pada percobaan ulang: ' . $e->getMessage()
                ];
            }
        }

        // Tampilkan hasil pengiriman ulang
        if (!empty($errors)) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'partial_success',
                'message' => 'Beberapa Virtual Account berhasil dikirim ulang, namun ada yang masih gagal',
                'errors' => $errors,
                'success_responses' => $responses
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Semua Virtual Account yang gagal berhasil dikirim ulang',
                'responses' => $responses
            ]);
        }
    }

    public function create_virtual_account_manual_siarraafi()
    {
        $partnerServiceId = '03636';//03636
        $customerNo = $this->input->post('customerNo');
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $virtualAccountName = $this->input->post('virtualAccountName');
        $totalAmount = $this->input->post('totalAmount');
        $totalAmountCurrency = 'IDR';
        $trxId = $this->input->post('trxId');
        $additionalInfo = $this->input->post('additionalInfo');
        $expiredDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $expiredDate->modify('+7 years');
        $expiredDateWithTimezone = $expiredDate->format('Y-m-d\TH:i:sP');
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $startDate = $currentDateTime->format('Y-m-d');
        $existingAccountData = $this->VirtualAccountModel->get_existing_partnumber();
        $partNumber = $existingAccountData ? $existingAccountData->partNumber + 1 : 1;
        $partnerReferenceNo = $this->generate_unique_payment_id() . $partNumber;
        $data = [
            'partnerServiceId' => $partnerServiceIdWithSpaces,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'totalAmount' => $totalAmount,
            'totalAmountCurrency' => $totalAmountCurrency,
            'startDate' => $startDate,
            'expiredDate' => $expiredDateWithTimezone,
            'trxId' => $trxId,
            'additionalInfo' => $additionalInfo,
            'trxDateTime' => $trxDateTimeFormatted,
            'partnerReferenceNo' => $partnerReferenceNo,
            'partNumber' => $partNumber
        ];

        $response = $this->api->create_virtual_account(
            $data['partnerServiceId'],
            $data['customerNo'],
            $data['virtualAccountNo'],
            $data['virtualAccountName'],
            $data['totalAmount'],
            $data['totalAmountCurrency'],
            $data['expiredDate'],
            $data['trxId'],
            $data['additionalInfo']
        );
        if (isset($response['error'])) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Failed to create Virtual Account: ' . $response['error'],
                'response' => $response
            ]);
        } else {
            $save_status = $this->VirtualAccountModel->save_virtual_account($data);

            if ($save_status) {
                echo json_encode(['status' => 'success', 'data' => $response]);
            } else {
                $this->output->set_status_header(500);
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to save Virtual Account to database'
                ]);
            }
        }
    }







    public function create_virtual_account_manual()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }
        $partnerServiceId = '22084';
        $customerNo = $this->input->post('customerNo');
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $virtualAccountName = $this->input->post('virtualAccountName');
        $totalAmount = $this->input->post('totalAmount');
        $totalAmountCurrency = 'IDR';
        $trxId = $this->input->post('trxId');
        $additionalInfo = $this->input->post('additionalInfo');
        $expiredDate = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $expiredDate->modify('+7 years');
        $expiredDateWithTimezone = $expiredDate->format('Y-m-d\TH:i:sP');
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $startDate = $currentDateTime->format('Y-m-d');
        $existingAccountData = $this->VirtualAccountModel->get_existing_partnumber();
        $partNumber = $existingAccountData ? $existingAccountData->partNumber + 1 : 1;
        $partnerReferenceNo = $this->generate_unique_payment_id() . $partNumber;
        $inquiryRequestId = $this->generate_unique_payment_id();
        $data = array(
            'partnerServiceId' => $partnerServiceIdWithSpaces,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'totalAmount' => $totalAmount,
            'totalAmountCurrency' => $totalAmountCurrency,
            'startDate' => $startDate,
            'expiredDate' => $expiredDateWithTimezone,
            'trxId' => $trxId,
            'additionalInfo' => $additionalInfo,
            'trxDateTime' => $trxDateTimeFormatted,
            'partnerReferenceNo' => $partnerReferenceNo,
            'partNumber' => $partNumber,
            'inquiryRequestId' => $inquiryRequestId
        );
    
        // Memanggil API untuk membuat virtual account
        $response = $this->api->create_virtual_account(
            $data['partnerServiceId'],
            $data['customerNo'],
            $data['virtualAccountNo'],
            $data['virtualAccountName'],
            $data['totalAmount'],
            $data['totalAmountCurrency'],
            $data['expiredDate'],
            $data['trxId'],
            $data['additionalInfo']
        );
    
        // Memastikan response adalah array
        $response = json_decode(json_encode($response), true);

// Mengecek jika response code selain 2002700 dianggap error
if (isset($response['responseCode']) && $response['responseCode'] != '2002700') {
    // Menyampaikan response error berdasarkan responseCode
    $this->output->set_status_header(500);
    echo json_encode(array(
        'status' => 'error',  // Menambahkan status error
        'responseCode' => $response['responseCode'],  // Menampilkan responseCode selain 2002700
        'responseMessage' => isset($response['responseMessage']) ? $response['responseMessage'] : 'No message'  // Mengambil responseMessage atau fallback jika tidak ada
    ));
} else {
    $save_status = $this->VirtualAccountModel->save_virtual_account($data);
    if ($save_status) {
        echo json_encode(array('status' => 'success', 'data' => $response));  // Mengirimkan response sukses
    } else {
        $this->output->set_status_header(500);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Failed to save Virtual Account to database'  // Mengirimkan pesan error jika gagal menyimpan ke database
        ));
    }
}

    }
    


    public function get_current_datetime()
    {
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $formattedDateTime = $currentDateTime->format('Y-m-d\TH:i');
        echo json_encode(['datetime' => $formattedDateTime]);
    }
    public function inquire_virtual_account()
{
    
    $virtualAccounts = $this->VirtualAccountModel->get_all_virtual_accounts();
    $responses = [];

    if ($virtualAccounts) {
        foreach ($virtualAccounts as $virtualAccount) {
            $data = [
                'partnerServiceId' => $virtualAccount->partnerServiceId,
                'customerNo' => $virtualAccount->customerNo,
                'virtualAccountNo' => $virtualAccount->virtualAccountNo,
                'trxId' => $virtualAccount->trxId
            ];

            // Panggil API untuk inquiry virtual account
            $response = $this->api->inquiry_virtual_account(
                $data['partnerServiceId'],
                $data['customerNo'],
                $data['virtualAccountNo'],
                $data['trxId']
            );

            // Menambahkan data tambahan ke response
            $paidStatus = $this->VirtualAccountModel->get_paid_status($data['customerNo']);
            $partnerReferenceNo = $this->VirtualAccountModel->get_partnerReferenceNo($data['customerNo']);
            $Status = $this->VirtualAccountModel->get_Status($data['customerNo']);
            $response['virtualAccountData']['paidStatus'] = $paidStatus ? $paidStatus : 'No Data';
            $response['virtualAccountData']['Status'] = $Status ? $Status : 'No Data';
            $response['virtualAccountData']['partnerReferenceNo'] = $partnerReferenceNo ? $partnerReferenceNo : 'No Data';

            // Tambahkan response ke array responses
            $responses[] = $response;
        }

        // Tampilkan hasil akhir dalam format JSON
        echo json_encode($responses);
    } else {
        // Jika tidak ada data akun virtual, tampilkan pesan error
        echo json_encode([
            "data" => [],
            "message" => "No virtual accounts found"
        ]);
    }
}



    public function update_status_va_controller()
    {
        $customerNo = $this->input->GET('customerNo');
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_customer_no($customerNo);
        if ($virtualAccount) {
            $data = [
                'partnerServiceId' => $virtualAccount->partnerServiceId,
                'customerNo' => $virtualAccount->customerNo,
                'virtualAccountNo' => $virtualAccount->virtualAccountNo,
                'trxId' => $virtualAccount->trxId,
                'paidStatus' => $virtualAccount->paidStatus
            ];
            $response = $this->api->update_status_va(
                $data['partnerServiceId'],
                $data['customerNo'],
                $data['virtualAccountNo'],
                $data['trxId'],
                $data['paidStatus']
            );
            if ($response && $response['responseCode'] === '2002900') {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Virtual account status updated successfully',
                    'virtualAccountData' => [
                        'paidStatus' => $response['virtualAccountData']['paidStatus']
                    ]
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to update virtual account status.',
                    'responseCode' => isset($response['responseCode']) ? $response['responseCode'] : null
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No virtual accounts found for customerNo: " . $customerNo
            ]);
        }
    }



    public function delete_va_controller()
    {
        $customerNo = $this->input->post('customerNo');
        if (empty($customerNo)) {
            echo json_encode([
                "status" => "error",
                "message" => "Customer number is required."
            ]);
            return;
        }

        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_customer_no($customerNo);
        if ($virtualAccount) {
            $data = [
                'partnerServiceId' => $virtualAccount->partnerServiceId,
                'customerNo' => $virtualAccount->customerNo,
                'virtualAccountNo' => $virtualAccount->virtualAccountNo,
                'trxId' => $virtualAccount->trxId
            ];
            $response = $this->api->delete_va(
                $data['partnerServiceId'],
                $data['customerNo'],
                $data['virtualAccountNo'],
                $data['trxId']
            );
            if (isset($response['error'])) {
                echo json_encode([
                    "status" => "error",
                    "message" => $response['error']
                ]);
            } else {
                $this->VirtualAccountModel->delete_virtual_account($customerNo);
                echo json_encode([
                    "status" => "success",
                    "message" => "Virtual account successfully deleted.",
                    "data" => $response
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No virtual account found for the given customer number."
            ]);
        }
    }

    
    public function get_report_va_controller()
    {
        $partnerServiceId = '22084';
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $startDate = $this->input->post('startDate');
        $startTime = '00:00:00+07:00';
        $endTime = '23:59:59+07:00';

        $data = [
            'partnerServiceId' => $partnerServiceIdWithSpaces,
            'startDate' => $startDate,
            'startTime' => $startTime,
            'endTime' => $endTime
        ];
        $response = $this->api->get_report_va(
            $data['partnerServiceId'],
            $data['startDate'],
            $data['startTime'],
            $data['endTime']
        );
        echo json_encode($response);

    }

    public function process_daily_reports($virtualAccountNo = null)
    {

        $virtualAccounts = $this->VirtualAccountModel->get_process_daily_reports($virtualAccountNo);
        if ($virtualAccounts) {
            foreach ($virtualAccounts as $row) {
                $partnerServiceId = $row->partnerServiceId;
                $startDate = $row->startDate;
                $data = array(
                    'partnerServiceId' => $partnerServiceId,
                    'startDate' => $startDate,
                    'startTime' => '00:00:00+07:00',
                    'endTime' => '23:59:59+07:00'
                );
                $response = $this->api->get_report_va(
                    $data['partnerServiceId'],
                    $data['startDate'],
                    $data['startTime'],
                    $data['endTime']
                );
                echo json_encode($response);
            }
        } else {
            echo "No virtual accounts found.";
        }
    }

    public function inquiry_status_va_controller()
    {
        $virtualAccounts = $this->VirtualAccountModel->get_all_virtual_accounts();
        $responses = [];

        if (!empty($virtualAccounts)) {
            foreach ($virtualAccounts as $virtualAccount) {
                $data = [
                    'partnerServiceId' => $virtualAccount->partnerServiceId,
                    'customerNo' => $virtualAccount->customerNo,
                    'virtualAccountNo' => $virtualAccount->virtualAccountNo,
                    'inquiryRequestId' => $virtualAccount->inquiryRequestId,
                ];
                $response = $this->api->inquiry_status_va(
                    $data['partnerServiceId'],
                    $data['customerNo'],
                    $data['virtualAccountNo'],
                    $data['inquiryRequestId']
                );
                $responses[] = $response;
            }
            echo json_encode($responses);
        } else {
            echo json_encode([
                "data" => [],
                "message" => "No partner Service Id found"
            ]);
        }
    }




    public function get_virtual_account_data()
    {
        $customerNo = $this->input->post('customerNo');
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_customer_no($customerNo);
        echo json_encode([
            "customerNo" => $virtualAccount->customerNo,
            "virtualAccountName" => $virtualAccount->virtualAccountName,
            "totalAmount" => $virtualAccount->totalAmount,
            "expiredDate" => $virtualAccount->expiredDate,
            "trxId" => $virtualAccount->trxId,
            "totalAmountCurrency" => 'IDR',
            "additionalInfo" => $virtualAccount->additionalInfo,
            "partnerReferenceNo" => $virtualAccount->partnerReferenceNo,
            "Status" => $virtualAccount->Status
        ]);
    }

    public function get_virtual_account_data_simulator()
    {
        $virtualAccountNo = $this->input->post('virtualAccountNo');
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_virtualAccount_No_simulator($virtualAccountNo);
        if ($virtualAccount) {
            echo json_encode([
                "virtualAccountNo" => $virtualAccount->virtualAccountNo,
                "customerNo" => $virtualAccount->customerNo,
                "partnerServiceId" => $virtualAccount->partnerServiceId,
                "virtualAccountName" => $virtualAccount->virtualAccountName,
                "totalAmount" => $virtualAccount->totalAmount,
                "expiredDate" => $virtualAccount->expiredDate,
                "trxId" => $virtualAccount->trxId,
                "totalAmountCurrency" => 'IDR',
                "additionalInfo" => $virtualAccount->additionalInfo,
                "paidStatus" => $virtualAccount->paidStatus,
                "partNumber" => $virtualAccount->partNumber,
                "partnerReferenceNo" => $virtualAccount->partnerReferenceNo
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data virtual account tidak ditemukan']);
        }
    }

    function generate_unique_payment_id()
    {
        $timestamp = time();
        $random = mt_rand(1000, 9999);
        return $timestamp . $random;
    }

    public function process_payment_transfer_to_va_simulator()
    {
        $partnerServiceId = $this->input->post('partnerServiceId');
        $customerNo = $this->input->post('customerNo');
        $virtualAccountNo = $this->input->post('virtualAccountNo');
        $totalAmountInput = $this->input->post('totalAmountInput');
        $virtualAccountName = $this->input->post('virtualAccountName');
        $paidAmountCurrency = 'IDR';
        $sourceAccountNo = '123';
        $partnerReferenceNo = $this->input->post('partnerReferenceNo');
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');
        $paidAmount = [
            'value' => $totalAmountInput,
            'currency' => $paidAmountCurrency
        ];
        $paymentResponse = $this->api->payment_va(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo,
            $virtualAccountName,
            $sourceAccountNo,
            $partnerReferenceNo,
            $paidAmount,
            $trxDateTimeFormatted
        );


        if (isset($paymentResponse['responseCode']) && $paymentResponse['responseCode'] == '2003300') {
            $this->VirtualAccountModel->save_payment($paymentResponse['virtualAccountData']);
            $output = [
                'status' => true,
                'message' => 'Pembayaran berhasil.',
                'data' => $paymentResponse
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($output));
        } else {
            $output = [
                'status' => false,
                'message' => isset($paymentResponse['responseMessage'])
                    ? $paymentResponse['responseMessage']
                    : 'Terjadi kesalahan saat memproses pembayaran.',
                'data' => $paymentResponse
            ];
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode($output));
        }
    }


    public function update_status_va_simulator()
    {
        $virtualAccountNo = $this->input->post('virtualAccountNo');
        $newPaidStatus = $this->input->post('paidStatus');

        if (empty($virtualAccountNo)) {
            echo json_encode(array('status' => 'error', 'message' => 'Nomor virtual account tidak valid'));
            return;
        }
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_virtualAccount_No_simulator($virtualAccountNo);

        if (!$virtualAccount) {
            echo json_encode(array('status' => 'error', 'message' => 'Virtual account tidak ditemukan'));
            return;
        }
        if ($newPaidStatus === $virtualAccount->paidStatus) {
            log_message('info', 'PaidStatus sudah sama, tidak perlu diperbarui di database untuk VA: ' . $virtualAccountNo);
            echo json_encode(array('status' => 'success', 'message' => 'Status sudah up-to-date'));
            return;
        }
        $updateData = array('paidStatus' => $newPaidStatus);
        $updateStatus = $this->VirtualAccountModel->update_virtual_account_simulator($virtualAccountNo, $updateData);
        if (!$updateStatus) {
            log_message('error', 'Gagal memperbarui status di database untuk VA: ' . $virtualAccountNo);
            echo json_encode(array('status' => 'error', 'message' => 'Gagal memperbarui status di database'));
            return;
        }
        $data = array(
            'partnerServiceId' => $virtualAccount->partnerServiceId,
            'customerNo' => $virtualAccount->customerNo,
            'virtualAccountNo' => $virtualAccount->virtualAccountNo,
            'trxId' => $virtualAccount->trxId,
            'paidStatus' => $newPaidStatus,
        );

        $response = $this->api->update_status_va(
            $data['partnerServiceId'],
            $data['customerNo'],
            $data['virtualAccountNo'],
            $data['trxId'],
            $data['paidStatus']
        );

        if ($response && isset($response['responseCode']) && $response['responseCode'] == '2002900') {
            log_message('info', 'Status VA berhasil diperbarui melalui API untuk VA: ' . $virtualAccountNo);
            echo json_encode(array(
                'status' => 'success',
                'message' => 'Status pembayaran berhasil diperbarui',
                'responseCode' => $response['responseCode']
            ));
        } else {
            $errorMessage = isset($response['responseMessage'])
                ? $response['responseMessage']
                : 'Gagal mengupdate status di API';

            log_message('error', 'Gagal memperbarui status melalui API: ' . $errorMessage);

            echo json_encode(array(
                'status' => 'error',
                'message' => $errorMessage,
                'responseCode' => isset($response['responseCode']) ? $response['responseCode'] : 'unknown'
            ));
        }

        log_message('debug', 'Updating paidStatus to: ' . $newPaidStatus . ' for ' . $virtualAccountNo);
    }

    public function update_virtual_account_manual()
    {
        $partNumber = $this->input->post('partNumber');
        $paidStatus = 'N';
        $customerNo = $this->input->post('customerNo');
        $virtualAccountName = $this->input->post('virtualAccountName');
        $totalAmount = $this->input->post('totalAmount');
        $expiredDateInput = $this->input->post('expiredDateInput');
        $trxId = $this->input->post('trxId');
        $additionalInfo = $this->input->post('additionalInfo');
        $totalAmountCurrency = 'IDR';
        $expiredDate = new DateTime($expiredDateInput, new DateTimeZone('Asia/Jakarta'));
        $expiredDateWithTimezone = $expiredDate->format('Y-m-d\TH:i:sP');
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_customer_no_and_partnumber_and_paidstatus($customerNo, $partNumber, $paidStatus);
        $updateData = [
            'virtualAccountName' => $virtualAccountName,
            'trxId' => $trxId,
            'totalAmount' => $totalAmount,
            'expiredDate' => $expiredDateWithTimezone,
            'additionalInfo' => $additionalInfo
        ];
        $updateStatus = $this->VirtualAccountModel->update_virtual_account($customerNo, $updateData);
        if ($updateStatus) {
            $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_customer_no($customerNo);
            $post = [
                'partnerServiceId' => $virtualAccount->partnerServiceId,
                'customerNo' => $virtualAccount->customerNo,
                'virtualAccountNo' => $virtualAccount->virtualAccountNo,
                'virtualAccountName' => $virtualAccountName,
                'trxId' => $trxId,
                'totalAmount' => $totalAmount,
                'totalAmountCurrency' => $totalAmountCurrency,
                'expiredDate' => $expiredDateWithTimezone,
                'additionalInfo' => $additionalInfo
            ];
            $response = $this->api->update_va(
                $post['partnerServiceId'],
                $post['customerNo'],
                $post['virtualAccountNo'],
                $post['virtualAccountName'],
                $post['totalAmount'],
                $post['totalAmountCurrency'],
                $post['expiredDate'],
                $post['trxId'],
                $post['additionalInfo']
            );
            echo json_encode($response);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan data ke database']);
        }
    }


}


