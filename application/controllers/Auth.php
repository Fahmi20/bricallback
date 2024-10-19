<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('AuthModel');
    }
    public function index()
    {
        $this->load->view('auth/login');
    }

    public function prosesLogin()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $query = $this->AuthModel->cek_user($email);

        if ($query) {
            //jika usernya aktif
            if ($query['is_active'] == 1) {
                //cek password
                if (password_verify($password, $query['password'])) {
                    $data = [
                        'email' => $query['email'],
                        'id_user' => $query['id_user'],
                        'foto' => $query['foto'],
                        'nama_user' => $query['nama_user'],
                        'no_telp' => $query['no_telp'],
                        'role' => $query['role'],
                        'id_departement' => $query['id_departement'],
                        'is_login' => TRUE,
                    ];
                    $this->session->set_userdata($data);
                    if ($query['role'] == 'Inventory') {
                        $this->session->set_flashdata('success', 'Anda Berhasil Login!');
                        redirect('Callback');
                    } elseif ($query['role'] == 'Produksi') {
                        $this->session->set_flashdata('success', 'Anda Berhasil Login!');
                        redirect('Produksi');
                    } elseif ($query['role'] == 'Manager') {
                        $this->session->set_flashdata('success', 'Anda Berhasil Login!');
                        redirect('Manager');
                    } else {
                        $this->session->unset_userdata('email');
                        $this->session->unset_userdata('id_user');
                        $this->session->unset_userdata('foto');
                        $this->session->unset_userdata('nama_user');
                        $this->session->unset_userdata('no_telp');
                        $this->session->unset_userdata('role');
                        $this->session->unset_userdata('id_departement');
                        $this->session->sess_destroy();
                        session_destroy();
                        redirect('/');
                    }
                } else {
                    $this->session->set_flashdata('error', 'Wrong Password!');
                    redirect('/');
                }
            } else {
                $this->session->set_flashdata('error', 'Email is Not Been Activated!');
                redirect('/');
            }
        } else {
            $this->session->set_flashdata('error', 'Email is Not Registered');
            redirect('/');
        }
    }

    public function Logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('foto');
        $this->session->unset_userdata('id_user');
        $this->session->unset_userdata('nama_user');
        $this->session->unset_userdata('no_telp');
        $this->session->unset_userdata('role');
        $this->session->unset_userdata('id_departement');
        $this->session->sess_destroy();
        session_destroy();
        $this->session->set_flashdata('success', 'Terimakasih Telah Menggunakan Aplikasi Ini');
        redirect('/');
    }
}
