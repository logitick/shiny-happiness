<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Payment extends CI_Controller {

    public function __construct(){
        parent::__construct();
    }

    public function index()
    {
        if($this->authentication->is_logged_in()){
            $this->layout->admin_home_view('admin/paymentHistory_listing');
        } else {
            $this->authentication->admin_redirects();
        }
    }

    /*load for ajax request*/
    public function load_payment_history_via_ajax($type = null){
        
        $this->load->view('admin/paymentHistory_listing');
        
    }

    public function getPaymentHistoryAjax()
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */

        $sColumns = array("USER.first_name"
                        , "USER.last_name"
                        , "PAYMENT.amount"
                        , "PAYMENT.payment_type"
                        , "CAMPAIGN.page_title"
                        , "PAYMENT.comment"
                        , "PAYMENT.payment_status"
                        , "PAYMENT.created");

        $aColumns = array("first_name"
                        , "last_name"
                        , "amount"
                        , "payment_type"
                        , "page_title"
                        , "comment"
                        , "payment_status"
                        , "created");
    
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
                    // $dir = $this->db->escape_str($sSortDir);
                    // $iSortCol++;
                    // $this->db->_protect_identifiers = FALSE;
                    // $this->db->order_by("$iSortCol $dir");
                        if($iSortCol == 0){
                            $this->db->order_by("USER.first_name", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 1){
                            $this->db->order_by("USER.last_name", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 2){
                            $this->db->order_by("PAYMENT.amount", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 3){
                            $this->db->order_by("PAYMENT.payment_type", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 4){
                            $this->db->order_by("CAMPAIGN.page_title", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 5){
                            $this->db->order_by("PAYMENT.comment", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 6){
                            $this->db->order_by("PAYMENT.payment_status", $this->db->escape_str($sSortDir));
                        } else if($iSortCol == 7){
                            $this->db->order_by("PAYMENT.created", $this->db->escape_str($sSortDir));
                        }
                        // $this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));    
                        
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

                    $this->db->like("USER.first_name", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("USER.last_name", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("PAYMENT.amount", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("PAYMENT.payment_type", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("CAMPAIGN.page_title", $this->db->escape_like_str($sSearch))  ;
                    $this->db->or_like("PAYMENT.comment", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("PAYMENT.payment_status", $this->db->escape_like_str($sSearch));
                    $this->db->or_like("PAYMENT.created", $this->db->escape_like_str($sSearch)); 
                }
        }
        
        // Select Data
        $this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $sColumns)), false);
        $this->db->from("tbl_users_payment_history AS PAYMENT");
        $this->db->join("tbl_users AS USER", "PAYMENT.user_id = USER.id", 'left');
        $this->db->join("tbl_user_campaigns AS CAMPAIGN", "PAYMENT.user_campaign_id = CAMPAIGN.id", 'left');
        $rResult = $this->db->get();

        // Data set length after filtering
        $this->db->select('FOUND_ROWS() AS found_rows');
        $iFilteredTotal = $this->db->get()->row()->found_rows;

        // Total data set length
        $iTotal = $this->db->count_all('tbl_users_payment_history');

        // Output
        $output = array(
            'sEcho' => intval($sEcho),
            'iTotalRecords' => $iTotal, 
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData' => array()
        );
        
        foreach($rResult->result_array() as $aRow) {
            $row = array();
            foreach($aColumns as $col) {
                $row[$col] = $aRow[$col];                
            }
            $output['aaData'][] = $row;
        }
    
        echo json_encode($output);
    }
    
}