<?php
class AuthModel extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function cek_user($email)
    {
        return $this->db->get_where('user', ['email' => $email])->row_array();
    }

    function cekLoginInventory()
    {
        if (empty($this->session->userdata('is_login'))) {
            $this->session->set_flashdata('error', 'Anda Harus Login Terlebihdahulu!');
            redirect('/');
        } elseif ($this->session->userdata('role') == 'Produksi') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Produksi');
        } elseif ($this->session->userdata('role') == 'Manager') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Manager');
        }
    }
    function cekLoginProduksi()
    {
        if (empty($this->session->userdata('is_login'))) {
            $this->session->set_flashdata('error', 'Anda Harus Login Terlebihdahulu!');
            redirect('/');
        } elseif ($this->session->userdata('role') == 'Inventory') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Callback');
        } elseif ($this->session->userdata('role') == 'Manager') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Manager');
        }
    }

    function cekLoginManager()
    {
        if (empty($this->session->userdata('is_login'))) {
            $this->session->set_flashdata('error', 'Anda Harus Login Terlebihdahulu!');
            redirect('/');
        } elseif ($this->session->userdata('role') == 'Inventory') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Callback');
        } elseif ($this->session->userdata('role') == 'Produksi') {
            $this->session->set_flashdata('error', 'Bukan Hak Akses Anda!');
            redirect('Produksi');
        }
    }
}
