<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_testimonials extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model("Testimonials");
        $this->load->model("User");
    }

    public function index()
    {
        if($this->authentication->is_logged_in()){
            $this->layout->admin_home_view('admin/testimonials_listing');
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

        $id = $this->input->post('testimonial_id');
        $action = $this->input->post('testimonial_action');
        if ($action == 'activate') {
           $this->activatePHP($id);
        } else if ($action == 'deactivate') {
            $this->deactivatePHP($id);
        } else if ($action == 'delete') {
            $this->deletePHP($id);
        }
        //$this->layout->admin_home_view('admin/testimonials_listing');
        //redirect('admin/withdraw');

    }

    public function activatePHP($id) {

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

    public function deactivatePHP($id) {

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

    public function deletePHP($id) {

        $this->Withdraws->deleteWithdraw($id);
        redirect('admin/withdraw');

    }

    # Ajax

    public function load_testimonials_via_ajax($type = null){
        
        $this->load->view('admin/testimonials_listing');
        
    }

    public function activateAjax() {
    
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

    public function deactivateAjax() {
    
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

    public function deleteAjax() {
    
        if($this->input->is_ajax_request()) {

            $this->Withdraws->deleteWithdraw($this->input->post('id'));
            echo json_encode(array('message' => 'success', 'text' => 'The withdraw amount request has been deleted.'));

        }

    }

    public function getTestimonialsAjax() {
    
        if($this->input->is_ajax_request()) {

            /* Array of database columns which should be read and sent back to DataTables. Use a space where
             * you want to insert a non-database field (for example a counter or static image)
             */

            $aColumns = array("first_name"
                            , "last_name"
                            , "type"
                            , "description"
                            , "status"
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
                        $this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));
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
                $bSearchable = $this->input->get_post('bSearchable_'.$i, true);  
                
                // Individual column filtering
                if(isset($bSearchable) && $bSearchable == 'true') {  
                    $this->db->like("first_name", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("last_name", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("type", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("description", $this->db->escape_like_str($sSearch));
                }
            }
            
            $sTable = "tbl_testimonials";

            // Select Data
            $this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $aColumns)), false);
            $rResult = $this->db->get($sTable);
        
            // Data set length after filtering
            $this->db->select('FOUND_ROWS() AS found_rows');
            $iFilteredTotal = $this->db->get()->row()->found_rows;
        
            // Total data set length
            $iTotal = $this->db->count_all($sTable);

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