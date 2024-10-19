<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Manager extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel');
        $this->load->model('AdminModel');
        $this->AuthModel->cekLoginManager();
    }

    public function index()
    {
        $this->template->load('layout/main', 'manager/dashboard');
    }

    public function Laporan_Barang_Masuk()
    {
        $this->template->load('layout/main', 'manager/laporan_barang_masuk');
    }

    public function Laporan_Barang_Keluar()
    {
        $this->template->load('layout/main', 'manager/laporan_barang_keluar');
    }

    public function Laporan_Permintaan_Barang()
    {
        $this->template->load('layout/main', 'manager/laporan_permintaan_barang');
    }

    public function Laporan_PO()
    {
        $this->template->load('layout/main', 'manager/laporan_po');
    }

    public function Laporan_Stok_Barang()
    {
        $this->template->load('layout/main', 'manager/laporan_stok_barang');
    }

    public function Profile()
    {
        $this->template->load('layout/main', 'manager/profile');
    }
}
