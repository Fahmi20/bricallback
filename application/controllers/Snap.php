<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Snap extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->library('SnapOAuth');
    }

    public function access_token_b2b()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            show_404();
        }
        $postData = $this->input->raw_input_stream;
        echo json_encode(['status' => 'success', 'message' => 'Request berhasil diproses']);
    }
}
