<?php
class VirtualAccountModel extends CI_Model
{
    public function save_virtual_account($data)
    {
        return $this->db->insert('virtual_accounts', $data);
    }

    public function saveAccessToken($clientID, $accessToken, $expiresIn) {
        $data = [
            'client_id' => $clientID,
            'access_token' => $accessToken,
            'expires_in' => $expiresIn,
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('access_tokens', $data);
        return $this->db->insert_id();
    }

    public function getAccessTokenByToken($accessToken) {
        $this->db->where('access_token', $accessToken);
        $query = $this->db->get('access_tokens');
        if ($query->num_rows() > 0) {
            return $query->row();
        }
        return false;
    }


    public function save_notification($data)
    {
        $this->db->insert('notification', $data);
        return $this->db->insert_id();
    }

    public function get_all_notifications()
    {
        return $this->db->get('push_notifications')->result_array();
    }

    public function save_payment($data)
    {
        $insert_data = array(
            'partner_service_id' => $data['partnerServiceId'],
            'customer_no' => $data['customerNo'],
            'virtual_account_no' => $data['virtualAccountNo'],
            'virtual_account_name' => $data['virtualAccountName'],
            'partner_reference_no' => $data['partnerReferenceNo'],
            'paid_amount_value' => $data['paidAmount']['value'],
            'paid_amount_currency' => $data['paidAmount']['currency'],
            'trx_date_time' => $data['trxDateTime'],
            'payment_request_id' => $data['paymentRequestId']
        );

        return $this->db->insert('virtual_account_payments', $insert_data);
    }

    public function savePaymentData($data) {
        // Simpan ke tabel payments (sesuaikan dengan struktur tabel Anda)
        $insertData = [
            'partner_serviceid' => $data['partnerServiceId'],
            'customer_no' => $data['customerNo'],
            'virtual_account' => $data['virtualAccountNo'],
            'trx_date' => $data['trxDateTime'],
            'payment_requestid' => $data['paymentRequestId'],
            'amount' => $data['additionalInfo']['paymentAmount'],
            'terminalid' => $data['additionalInfo']['terminalId'],
            'bankid' => $data['additionalInfo']['bankId'],
            'status' => 'PAID',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Masukkan data ke database
        return $this->db->insert('payments', $insertData);
    }


    public function get_last_customer_no()
    {
        $this->db->select('customerNo');
        $this->db->order_by('customerNo', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('virtual_accounts');

        if ($query->num_rows() > 0) {
            return $query->row()->customerNo;
        } else {
            return null;
        }
    }

    public function get_existing_partnumber()
    {
        $this->db->select('partNumber');
        $this->db->from('virtual_accounts');
        $this->db->order_by('partNumber', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get();

        if ($query !== false && $query->num_rows() > 0) {
            return $query->row()->partNumber;
        } else {
            return null;
        }
    }

    public function get_virtual_account_by_customer_no_and_partnumber_and_paidstatus($customerNo, $partNumber, $paidStatus)
    {
        $this->db->where('customerNo', $customerNo);
        $this->db->where('paidStatus', 'N');
        $this->db->order_by('partNumber', 'ASC');
        $query = $this->db->get('virtual_accounts');

        if ($query->num_rows() > 0) {
            return $query->row();  // Mengembalikan satu baris sebagai objek
        } else {
            return false;  // Tidak ditemukan
        }
    }





    public function get_virtual_account_by_customer_no($customerNo)
    {
        $this->db->where('customerNo', $customerNo);
        $query = $this->db->get('virtual_accounts');

        if ($query->num_rows() > 0) {
            return $query->row();  // Mengembalikan satu baris sebagai objek
        } else {
            return false;
        }
    }

    public function delete_virtual_account($customerNo)
    {
        $this->db->where('customerNo', $customerNo);
        $this->db->delete('virtual_accounts');
    }

    public function update_virtual_account($customerNo, $updateData)
    {
        log_message('debug', 'Updating virtual account for customerNo: ' . $customerNo);
        log_message('debug', 'Update data: ' . print_r($updateData, true));
        $this->db->where('customerNo', $customerNo);
        $this->db->update('virtual_accounts', $updateData);
        if ($this->db->affected_rows() > 0) {
            log_message('debug', 'Update successful for customerNo: ' . $customerNo);
            return true;
        } else {
            log_message('error', 'Update failed or no changes for customerNo: ' . $customerNo);
            return false;
        }
    }

    public function get_virtual_account_by_virtualAccount_No_simulator($virtualAccountNo)
    {
        $this->db->where('virtualAccountNo', $virtualAccountNo);
        $this->db->where('paidStatus', 'N');
        $this->db->order_by('partnerReferenceNo', 'ASC');
        $query = $this->db->get('virtual_accounts');

        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function insert_transaction($data)
    {
        $this->db->insert('transactions', $data);
        return $this->db->insert_id();
    }





    public function update_virtual_account_simulator($virtualAccountNo, $updateData)
    {
        log_message('debug', 'Updating virtual account for virtualAccountNo: ' . $virtualAccountNo);
        log_message('debug', 'Update data: ' . print_r($updateData, true));

        $this->db->where('virtualAccountNo', $virtualAccountNo);
        $this->db->update('virtual_accounts', $updateData);

        if ($this->db->affected_rows() > 0) {
            log_message('debug', 'Update successful for virtualAccountNo: ' . $virtualAccountNo);
            return true;
        } else {
            log_message('error', 'Update failed or no changes for virtualAccountNo: ' . $virtualAccountNo);
            return false;
        }
    }

    public function get_failed_virtual_accounts()
    {
        $this->db->where('resend', 1);
        return $this->db->get('virtual_accounts')->result_array();
    }

    public function update_virtual_account_status($id, $data)
    {
        $this->db->where('virtualAccountNo', $id);
        return $this->db->update('virtual_accounts', $data);
    }




    public function get_all_virtual_accounts()
    {
        $query = $this->db->get('virtual_accounts');
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return false;
        }
    }

    public function get_all_payment_virtual_accounts()
    {
        $query = $this->db->get('virtual_account_payments');
        return $query->result();
    }

    public function get_process_daily_reports($virtualAccountNo = null)
{
    $this->db->select('virtualAccountNo, partnerServiceId, startDate'); // Pilih kolom yang dibutuhkan

    if ($virtualAccountNo) {
        $this->db->where('virtualAccountNo', $virtualAccountNo); // Filter jika ada virtualAccountNo yang diberikan
    }

    $query = $this->db->get('virtual_accounts'); // Nama tabel virtual accounts

    if ($query->num_rows() > 0) {
        return $query->result(); // Kembalikan semua hasil
    }
    return false;
}



    public function get_paid_status($customerNo)
    {
        $this->db->select('paidStatus');
        $this->db->where('customerNo', $customerNo);
        $query = $this->db->get('virtual_accounts'); // Sesuaikan nama tabel Anda

        if ($query->num_rows() > 0) {
            return $query->row()->paidStatus;
        }
        return false;
    }

    public function get_partnerReferenceNo($customerNo)
    {
        $this->db->select('partnerReferenceNo');
        $this->db->where('customerNo', $customerNo);
        $query = $this->db->get('virtual_accounts'); // Sesuaikan nama tabel Anda

        if ($query->num_rows() > 0) {
            return $query->row()->partnerReferenceNo;
        }
        return false;
    }

    public function get_Status($customerNo)
    {
        $this->db->select('Status');
        $this->db->where('customerNo', $customerNo);
        $query = $this->db->get('virtual_accounts'); // Sesuaikan nama tabel Anda

        if ($query->num_rows() > 0) {
            return $query->row()->Status;
        }
        return false;
    }



    public function get_virtual_account_data($customerNo)
    {
        $this->db->select('partnerServiceId, customerNo, virtualAccountNo, trxId');
        $this->db->from('virtual_accounts');
        $this->db->where('customerNo', $customerNo);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            return $query->row();
        }

        return false;
    }

    public function count_all()
    {
        return $this->db->count_all('virtual_accounts');
    }

    public function count_filtered($searchValue)
    {
        if (!empty($searchValue)) {
            $this->db->like('customer_no', $searchValue);
            $this->db->or_like('virtual_account_no', $searchValue);
        }
        return $this->db->count_all_results('virtual_accounts');
    }

    public function get_filtered_data($start, $length, $orderByColumn, $orderDir, $searchValue)
    {
        if (!empty($searchValue)) {
            $this->db->like('customer_no', $searchValue);
            $this->db->or_like('virtual_account_no', $searchValue);
        }

        $this->db->order_by($orderByColumn, $orderDir);
        $this->db->limit($length, $start);
        $query = $this->db->get('virtual_accounts');
        return $query->result_array();
    }

    public function save_push_notification($data)
    {
        return $this->db->insert('push_notifications', $data);
    }

    public function get_push_notifications($limit = 10, $offset = 0)
    {
        $query = $this->db->get('push_notifications', $limit, $offset);
        return $query->result_array();
    }
}
