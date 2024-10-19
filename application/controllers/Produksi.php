<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Produksi extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel');
        $this->load->model('AdminModel');
        $this->AuthModel->cekLoginProduksi();
    }

    public function index()
    {
        $this->template->load('layout/main', 'produksi/dashboard');
    }

    public function Permintaan_Barang()
    {
        $this->template->load('layout/main', 'produksi/permintaan_barang');
    }

    public function Tambah_Request_Permintaan_Barang()
    {
        $this->template->load('layout/main', 'produksi/request_permintaan_barang_tambah');
    }

    public function Detail_Request_Permintaan_Barang($id)
    {
        $data['id'] = $id;
        $this->template->load('layout/main', 'produksi/request_permintaan_barang_detail', $data);
    }

    public function Edit_Request_Permintaan_Barang($id)
    {
        $data['id'] = $id;
        $this->template->load('layout/main', 'produksi/request_permintaan_barang_edit', $data);
    }

    public function Profile()
    {
        $this->template->load('layout/main', 'produksi/profile');
    }
}
