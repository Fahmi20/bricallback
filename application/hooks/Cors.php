<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cors {

    public function setCorsHeaders()
    {
        // Set headers for CORS
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
        header('Access-Control-Max-Age: 86400');
    }

}
