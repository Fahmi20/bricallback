<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Webhook extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    public function receive() {
        $inputData = file_get_contents('php://input');
        $data = json_decode($inputData, true);
        if ($data) {
            log_message('info', 'Data webhook: ' . print_r($data, true));
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
        }
    }
}
?>
