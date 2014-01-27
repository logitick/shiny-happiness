<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Withdraw extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("Withdraws");
        $this->load->model("User");
    }

    public function index()
    {
        if($this->authentication->is_logged_in()){
            $this->layout->admin_home_view('admin/withdrawRequest_listing');
        } else {
            $this->authentication->admin_redirects();
        }
    }

    public function loadViewConfirmationPHP($id, $sLabel) {

        $this->load->helper('form');
        $data['withdrawRequest_id'] = $id;
        $data['sLabel'] = $sLabel;
        $this->layout->admin_home_view('admin/withdrawRequest_confirmation', $data);

    }

    public function validateActionPHP() {

        $id = $this->input->post('withdrawRequest_approve_id');
        $action = $this->input->post('withdrawRequest_action');
        if ($action == 'confirm') {
           $this->approveRequestPHP($id);
        } else if ($action == 'delete') {
            $this->deleteRequestPHP($id);
        }
        //$this->layout->admin_home_view('admin/withdrawRequest_listing');
        //redirect('admin/withdraw');

    }

    public function approveRequestPHP($id) {

        $withdraw = $this->Withdraws->getWithdrawAmountById($id);
        $amount = floatval($withdraw->amount);
        $user = $this->User->getTotalEarningById($withdraw->user_id);
        $newTotalEarning = floatval($user->total_earning) - floatval($withdraw->amount);

        $data = array(
            'total_earning' => floatval($newTotalEarning)
            );
        $this->User->updateMyAccount($withdraw->user_id, $data);

        $data = array(
            'withdraw_status' => 'Confirm'
            );
        $this->Withdraws->updateWithdrawTbl($id, $data);

        // sending email notification

        $user = $this->User->getEmailAndPasswordById($withdraw->user_id);

        $email = $user->email_address;

        $user_query = $this->User->getUserByEmail($email);
        $user_query = $user_query->row();
        $username = $user_query->username;

        $this->load->helper('email_helper');
        $data = array(
            'username' => $username,
            'amount' => $withdraw->amount
            );
        if (email_helper('withdraw_request_confirm', $email, $data)) {
            redirect('admin/withdraw');
        }
        
        // end of sending email notification

    }

    public function deleteRequestPHP($id) {

        $this->Withdraws->deleteWithdraw($id);
        redirect('admin/withdraw');

    }

    # Ajax

    public function load_withdraw_request_via_ajax($type = null){
        
        $this->load->view('admin/withdrawRequest_listing');
        
    }

    public function approveRequestAjax() {
    
        if($this->input->is_ajax_request()) {

            $id = $this->input->post('id');
            $withdraw = $this->Withdraws->getWithdrawAmountById($id);
            $amount = floatval($withdraw->amount);
            $user = $this->User->getTotalEarningById($withdraw->user_id);
            $newTotalEarning = floatval($user->total_earning) - floatval($withdraw->amount);

            $data = array(
                'total_earning' => floatval($newTotalEarning)
                );
            $this->User->updateMyAccount($withdraw->user_id, $data);
            
            $data = array(
                'withdraw_status' => 'Confirm'
                );
            $this->Withdraws->updateWithdrawTbl($id, $data);
        
            // sending email notification

            $user = $this->User->getEmailAndPasswordById($withdraw->user_id);

            $email = $user->email_address;
            
            $user_query = $this->User->getUserByEmail($email);
            $user_query = $user_query->row();
            $username = $user_query->username;

            $this->load->helper('email_helper');
            $data = array(
                'username' => $username,
                'amount' => $withdraw->amount
                );
            if (email_helper('withdraw_request_confirm', $email, $data)) {  
                echo json_encode(array('message' => 'success', 'text' => 'approved', 'status' => 'Confirm'));
            } else {
                echo json_encode(array('message' => 'failed'));
            }

            // end of sending email notification
        }

    }

    public function deleteRequestAjax() {
    
        if($this->input->is_ajax_request()) {

            $this->Withdraws->deleteWithdraw($this->input->post('id'));
            echo json_encode(array('message' => 'success', 'text' => 'The withdraw amount request has been deleted.'));

        }

    }

    public function getWithdrawRequestAjax() {
    
        if($this->input->is_ajax_request()) {

            /* Array of database columns which should be read and sent back to DataTables. Use a space where
             * you want to insert a non-database field (for example a counter or static image)
             */

            $sColumns = array("U.username"
                            , "CONCAT('$', W.amount) as amount"
                            , "W.withdraw_status"
                            , "W.id");
            $aColumns = array("username"
                            , "amount"
                            , "withdraw_status"
                            , "id");
        
            $iDisplayStart = $this->input->get_post('iDisplayStart', true);
            $iDisplayLength = $this->input->get_post('iDisplayLength', true);
            $iSortCol_0 = $this->input->get_post('iSortCol_0', true);
            $iSortingCols = $this->input->get_post('iSortingCols', true);
            $sSearch = $this->input->get_post('sSearch', true);
            $sEcho = $this->input->get_post('sEcho', true);
        
            // Paging
            if(isset($iDisplayStart) && $iDisplayLength != '-1')
            {
                $this->db->limit($this->db->escape_str($iDisplayLength), $this->db->escape_str($iDisplayStart));
            }
            
            // Ordering
            if(isset($iSortCol_0))
            {
                for($i=0; $i<intval($iSortingCols); $i++)
                {
                    $iSortCol = $this->input->get_post('iSortCol_'.$i, true);
                    $bSortable = $this->input->get_post('bSortable_'.intval($iSortCol), true);
                    $sSortDir = $this->input->get_post('sSortDir_'.$i, true);

                    if($bSortable == 'true')
                    {                    

                        if($iSortCol == 1){
                            $this->db->order_by("W.amount", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 3){
                            $this->db->order_by("W.withdraw_status", $this->db->escape_str($sSortDir));
                        } else {
                            $this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));    
                        }
                    }
                }            
            }

            /* 
             * Filtering
             * NOTE this does not match the built-in DataTables filtering which does it
             * word by word on any field. It's possible to do here, but concerned about efficiency
             * on very large tables, and MySQL's regex functionality is very limited
             */
            if(isset($sSearch) && !empty($sSearch)) {
                for($i=0; $i<count($aColumns); $i++) {

                    $bSearchable = $this->input->get_post('bSearchable_'.$i, true);  
                    
                    // Individual column filtering
                    if(isset($bSearchable) && $bSearchable == 'true') {  

                        if($i == 0){
                            $this->db->or_like("U.username", $this->db->escape_like_str($sSearch));   
                        } else if($i == 1){
                            $this->db->or_like("CONCAT('$', W.amount)", $this->db->escape_like_str($sSearch));   
                        } else if($i == 2){
                            $this->db->or_like("W.withdraw_status", $this->db->escape_like_str($sSearch));   
                        }
                        
                    }
                }
            }
            
            // Select Data
            $this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $sColumns)), false);
            $this->db->from("tbl_withdraws W");
            $this->db->join("tbl_users U", "W.user_id = U.id");
            $rResult = $this->db->get();
        
            // Data set length after filtering
            $this->db->select('FOUND_ROWS() AS found_rows');
            $iFilteredTotal = $this->db->get()->row()->found_rows;
        
            // Total data set length
            $iTotal = $this->db->count_all('tbl_withdraws');

            // Output
            $output = array(
                'sEcho' => intval($sEcho),
                'iTotalRecords' => $iTotal, 
                'iTotalDisplayRecords' => $iFilteredTotal,
                'iSortCol' => $iSortCol,
                'aaData' => array()
            );
            
            foreach($rResult->result_array() as $aRow) {
                $row = array();
                foreach($aColumns as $col){
                    $row[$col] = $aRow[$col];                
                }        
                $output['aaData'][] = $row;
            }

            echo json_encode($output);
        }
    }
    
}