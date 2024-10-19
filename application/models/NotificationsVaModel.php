<?php
defined('BASEPATH') or exit('No direct script access allowed');

class NotificationsVaModel extends CI_Model
{
    // Nama tabel yang digunakan untuk menyimpan notifikasi BRIVA
    private $table = 'briva_notifications';

    // Mendapatkan notifikasi BRIVA dengan pagination, search, dan order
    public function get_briva_notifications($start, $length, $orderColumn, $orderDir)
    {
        $searchValue = $this->input->get('search')['value']; // Mendapatkan nilai pencarian

        $this->db->select('brivaNo, billAmount, transactionDateTime, status');
        $this->db->from($this->table);

        // Jika ada nilai pencarian, tambahkan kondisi pencarian
        if (!empty($searchValue)) {
            $this->db->group_start(); // Membuat grup untuk pencarian
            $this->db->like('brivaNo', $searchValue);
            $this->db->or_like('billAmount', $searchValue);
            $this->db->or_like('transactionDateTime', $searchValue);
            $this->db->or_like('status', $searchValue);
            $this->db->group_end();
        }

        // Menambahkan urutan berdasarkan kolom yang dipilih oleh DataTables
        $this->db->order_by($orderColumn, $orderDir);

        // Pagination, ambil data dengan batasan start dan length
        $this->db->limit($length, $start);

        $query = $this->db->get();
        return $query->result();
    }

    // Menghitung total seluruh notifikasi tanpa filter (untuk totalRecords)
    public function count_all_notifications()
    {
        return $this->db->count_all($this->table);
    }

    // Menghitung total notifikasi setelah dilakukan pencarian (untuk totalFiltered)
    public function count_filtered_notifications($searchValue)
    {
        $this->db->from($this->table);

        if (!empty($searchValue)) {
            $this->db->group_start();
            $this->db->like('brivaNo', $searchValue);
            $this->db->or_like('billAmount', $searchValue);
            $this->db->or_like('transactionDateTime', $searchValue);
            $this->db->or_like('status', $searchValue);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }
}
