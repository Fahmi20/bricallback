<?php
class AdminModel extends CI_Model
{
    // #################GENERAL#############################//

    function getData($database)
    {
        return $this->db->get($database)->result();
    }

    function createData($database, $data)
    {
        return $this->db->insert($database, $data);
    }

    function updateData($database, $data, $id)
    {
        return $this->db->update($database, $data, $id);
    }

    function deleteData($database, $id)
    {
        return $this->db->delete($database, $id);
    }

    function cekData($database, $data)
    {
        return $this->db->get_where($database, $data)->row_array();
    }

    function join($database, $database2, $data)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $data);
        return $this->db->get();
    }

    function doubleJoin($database, $database2, $database3, $data1, $data2)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $data1);
        $this->db->join($database3, $data2);
        return $this->db->get();
    }

    function tripleJoin($database, $database2, $database3, $database4, $data1, $data2, $data3)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $data1);
        $this->db->join($database3, $data2);
        $this->db->join($database4, $data3);
        return $this->db->get();
    }

    function getWhere($database, $data)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->where($data);
        return $this->db->get();
    }

    function join_Where($database, $database2, $join1, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $join1);
        $this->db->where($where);
        return $this->db->get();
    }


    function doubleJoin_Where($database, $database2, $database3, $join1, $join2, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $join1);
        $this->db->join($database3, $join2);
        $this->db->where($where);
        return $this->db->get();
    }

    function tripleJoin_Where($database, $database2, $database3, $database4, $join1, $join2, $join3, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $join1);
        $this->db->join($database3, $join2);
        $this->db->join($database4, $join3);
        $this->db->where($where);
        return $this->db->get();
    }

    function lastData($database, $data_last, $sort)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->order_by($data_last, $sort);
        return $this->db->get();
    }

    function getLimit($database, $where, $data_last, $sort)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->where($where);
        $this->db->order_by($data_last, $sort);
        $this->db->limit(1);
        return $this->db->get();
    }

    function sum_where($database, $where, $sum)
    {
        $this->db->select($sum);
        $this->db->from($database);
        $this->db->where($where);
        return $this->db->get();
    }

    function count_where($database, $count, $where)
    {
        $this->db->select($count);
        $this->db->from($database);
        $this->db->where($where);
        return $this->db->get();
    }

    function join_Where_Limit($database, $database2, $join1, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join($database2, $join1);
        $this->db->where($where);
        $this->db->limit(5);
        return $this->db->get();
    }

    function sum($database, $select)
    {
        $this->db->select($select);
        $this->db->from($database);
        return $this->db->get();
    }

    function get_barang($database, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->where($where);
        $this->db->join('barang', 'barang.id_barang = barang_masuk.id_barang');
        $this->db->order_by('tgl_datang', 'ASC');
        $this->db->order_by('exp', 'ASC');
        return $this->db->get();
    }

    function get_stok($database, $database2, $join1,  $where)
    {
        $this->db->select('SUM(qty_masuk) as Total, nama_barang, barang_masuk.id_barang');
        $this->db->from($database);
        $this->db->join($database2, $join1);
        $this->db->where($where);
        $this->db->group_by('id_barang');
        return $this->db->get();
    }

    function SearchBatch($database, $where)
    {
        $this->db->select('*');
        $this->db->from($database);
        $this->db->join('barang', 'barang.id_barang=barang_masuk.id_barang');
        $this->db->where('batch_number', $where);
        return $this->db->get()->row_array();
    }

    function getDetailPermintaan($id_permintaan, $id_barang)
    {
        $this->db->select('*');
        $this->db->from('detail_permintaan');
        $this->db->where('id_permintaan', $id_permintaan);
        $this->db->where('id_barang', $id_barang);
        return $this->db->get()->row_array();
    }
}
