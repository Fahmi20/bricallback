<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Backend extends CI_Controller
{
    private $allowed_origins = [
        'https://sandbox.bri.co.id'
    ];
    public function __construct()
    {
        parent::__construct();
        $this->load->library('api');
        $this->load->library('hit');
        $this->load->helper(['url', 'form']);
        $this->load->library('form_validation');
        $this->load->library('session');
        $this->load->model('VirtualAccountModel');
        $this->enable_cors();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function get_access_token()
    {
        $access_token = $this->api->get_access_token();

        if (is_array($access_token) && isset($access_token['error'])) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'error',
                'message' => $access_token['error'],
                'response' => $access_token['response']
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'access_token' => $access_token
            ]);
        }
    }

    public function get_access_token_push_notif()
    {
        $access_token = $this->api->get_push_notif_token();

        if (is_array($access_token) && isset($access_token['error'])) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'error',
                'message' => $access_token['error'],
                'response' => $access_token['response']
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'access_token' => $access_token
            ]);
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

        // Debugging untuk memastikan format benar
        var_dump($virtualAccountNo);  // Contoh: "    7777700000000000001"
        echo "Panjang virtualAccountNo: " . strlen($virtualAccountNo);  // Harus <= 28

        // Siapkan data untuk request
        $postData = [
            'partnerServiceId' => trim($partnerServiceId),  // Ditrim untuk dikirim tanpa spasi
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'amount' => '12345.00',
            'currency' => 'IDR',
            'inquiryRequestId' => uniqid('inq_')
        ];

        // Panggil API untuk inquiry payment
        $response = $this->api->inquiry_payment_va_briva(
            $postData['partnerServiceId'],
            $postData['customerNo'],
            $postData['virtualAccountNo'],
            $postData['amount'],
            $postData['inquiryRequestId']
        );

        // Tampilkan respons dalam format JSON
        echo json_encode($response);
    }

    public function get_inquiry_payment_va_briva_controller()
    {
        // Mengambil parameter dari input GET
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
            $partnerReferenceNo = '123';
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
        $partnerServiceId = '   03636';
        $customerNo = '565656';
        $virtualAccountNo = '   03636565656';
        $virtualAccountName = 'Fahmi';
        $sourceAccountNo = '123';
        $partnerReferenceNo = $this->generate_unique_payment_id();
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
        ;
        $customerNo = $this->input->post('customerNo');
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $customerNo = $this->input->post('customerNo');
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
                'partNumber' => $i
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
                $errors[] = [
                    'virtualAccountNo' => $virtualAccountNo,
                    'error' => $response['error']
                ];
            } else {
                $save_status = $this->VirtualAccountModel->save_virtual_account($data);

                if ($save_status) {
                    $responses[] = [
                        'virtualAccountNo' => $virtualAccountNo,
                        'status' => 'success',
                        'data' => $response
                    ];
                } else {
                    $errors[] = [
                        'virtualAccountNo' => $virtualAccountNo,
                        'error' => 'Failed to save to database'
                    ];
                }
            }
        }
        if (!empty($errors)) {
            $this->output->set_status_header(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Some Virtual Accounts failed to be created or saved',
                'errors' => $errors,
                'success_responses' => $responses
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'All Virtual Accounts created and saved successfully',
                'responses' => $responses
            ]);
        }
    }

    public function create_virtual_account_manual()
    {
        $partnerServiceId = '03636';//03636
        $customerNo = $this->input->post('customerNo');
        $partnerServiceIdWithSpaces = '   ' . $partnerServiceId;
        $virtualAccountNo = '   ' . $partnerServiceId . $customerNo;
        $virtualAccountName = $this->input->post('virtualAccountName');
        $totalAmount = $this->input->post('totalAmount');
        $totalAmountCurrency = 'IDR';
        $expiredDateInput = $this->input->post('expiredDate');
        $trxId = $this->input->post('trxId');
        $additionalInfo = $this->input->post('additionalInfo');
        $expiredDate = new DateTime($expiredDateInput, new DateTimeZone('Asia/Jakarta'));
        $expiredDate->modify('+7 years');
        $expiredDateWithTimezone = $expiredDate->format('Y-m-d\TH:i:sP');
        $trxDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $trxDateTimeFormatted = $trxDateTime->format('Y-m-d\TH:i:sP');
        $currentDateTime = new DateTime('now', new DateTimeZone('Asia/Jakarta'));
        $startDate = $currentDateTime->format('Y-m-d');
        $timezoneOffset = $currentDateTime->format('P');
        $startTime = $currentDateTime->format('H:i:s');
        $startTimeWithOffset = $startTime . $timezoneOffset;
        $endTime = $expiredDate->format('H:i:s');
        $endTimeWithOffset = $endTime . $timezoneOffset;
        $existingAccountData = $this->VirtualAccountModel->get_existing_partnumber();
        $partNumber = $existingAccountData ? $existingAccountData->partNumber + 1 : 1;
        $data = [
            'partnerServiceId' => $partnerServiceIdWithSpaces,
            'customerNo' => $customerNo,
            'virtualAccountNo' => $virtualAccountNo,
            'virtualAccountName' => $virtualAccountName,
            'totalAmount' => $totalAmount,
            'totalAmountCurrency' => $totalAmountCurrency,
            'startDate' => $startDate,
            'startTime' => $startTimeWithOffset,
            'endTime' => $endTimeWithOffset,
            'expiredDate' => $expiredDateWithTimezone,
            'trxId' => $trxId,
            'additionalInfo' => $additionalInfo,
            'trxDateTime' => $trxDateTimeFormatted,
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
                $response = $this->api->inquiry_virtual_account(
                    $data['partnerServiceId'],
                    $data['customerNo'],
                    $data['virtualAccountNo'],
                    $data['trxId']
                );
                $paidStatus = $this->VirtualAccountModel->get_paid_status($data['customerNo']);
                $partnerReferenceNo = $this->VirtualAccountModel->get_partnerReferenceNo($data['customerNo']);
                $response['virtualAccountData']['paidStatus'] = $paidStatus ? $paidStatus : 'No Data';
                $response['virtualAccountData']['partnerReferenceNo'] = $partnerReferenceNo ? $partnerReferenceNo : 'No Data';
                $responses[] = $response;
            }
            echo json_encode($responses);
        } else {
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
        $partnerServiceId = '00000';
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
                    'inquiryRequestId' => $this->generate_unique_payment_id()
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

    public function push_notification_controller()
    {
        $partnerServiceId = $this->input->post('partnerServiceId');
        $customerNo = $this->input->post('customerNo');
        $virtualAccountNo = $this->input->post('virtualAccountNo');
        $trxDateTimeInput = $this->input->post('expiredDate');
        $paymentRequestId = $this->input->post('paymentRequestId');
        $additionalInfo = $this->input->post('additionalInfo');

        if (empty($trxDateTimeInput) || empty($partnerServiceId) || empty($customerNo) || empty($virtualAccountNo)) {
            echo json_encode(['error' => 'Missing required parameters']);
            return;
        }

        try {
            $trxDateTime = new DateTime($trxDateTimeInput, new DateTimeZone('Asia/Jakarta'));
            $trxDateTimeWithTimezone = $trxDateTime->format('Y-m-d\TH:i:sP');
        } catch (Exception $e) {
            echo json_encode(['error' => 'Invalid date format']);
            return;
        }

        $response = $this->api->push_notif(
            $partnerServiceId,
            $customerNo,
            $virtualAccountNo,
            $trxDateTimeWithTimezone,
            $paymentRequestId,
            $additionalInfo
        );

        if (isset($response['error'])) {
            echo json_encode(['error' => $response['error']]);
        } else {
            $this->VirtualAccountModel->save_notification([
                'partnerServiceId' => $partnerServiceId,
                'customerNo' => $customerNo,
                'virtualAccountNo' => $virtualAccountNo,
                'trxDateTime' => $trxDateTimeWithTimezone,
                'paymentRequestId' => $paymentRequestId,
                'additionalInfo' => $additionalInfo,
                'status' => 'Sent'
            ]);

            echo json_encode(['success' => 'Notification sent successfully']);
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
            "partnerReferenceNo" => $virtualAccount->partnerReferenceNo
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
        $newPaidStatus = $this->input->post('paidStatus'); // Terima paidStatus dari AJAX

        if (empty($virtualAccountNo)) {
            echo json_encode(array('status' => 'error', 'message' => 'Nomor virtual account tidak valid'));
            return;
        }

        // Ambil virtual account dari database
        $virtualAccount = $this->VirtualAccountModel->get_virtual_account_by_virtualAccount_No_simulator($virtualAccountNo);

        if (!$virtualAccount) {
            echo json_encode(array('status' => 'error', 'message' => 'Virtual account tidak ditemukan'));
            return;
        }

        // Cek apakah paidStatus perlu diperbarui
        if ($newPaidStatus === $virtualAccount->paidStatus) {
            log_message('info', 'PaidStatus sudah sama, tidak perlu diperbarui di database untuk VA: ' . $virtualAccountNo);
            echo json_encode(array('status' => 'success', 'message' => 'Status sudah up-to-date'));
            return;
        }

        // Update paidStatus di database
        $updateData = array('paidStatus' => $newPaidStatus);
        $updateStatus = $this->VirtualAccountModel->update_virtual_account_simulator($virtualAccountNo, $updateData);

        if (!$updateStatus) {
            log_message('error', 'Gagal memperbarui status di database untuk VA: ' . $virtualAccountNo);
            echo json_encode(array('status' => 'error', 'message' => 'Gagal memperbarui status di database'));
            return;
        }

        // Kirim data ke API eksternal
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

    private function enable_cors()
    {
        $allowed_origins = ['https://sandbox.bri.co.id'];
        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        if (in_array($origin, $allowed_origins)) {
            header('Access-Control-Allow-Origin: ' . $origin);
        } else {
            header('Access-Control-Allow-Origin: *');
        }
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }


}


