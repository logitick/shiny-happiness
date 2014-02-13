<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Coupons extends CI_Controller {
	public function __construct() {
        parent::__construct();
        if(!$this->authentication->is_logged_in()){
            redirect(site_url('admin'));
            die();
        }

        $this->load->model('couponCodes');
        $this->load->model('campaignPackages');
        $this->load->model('user', 'User');
	}
	public function index() {
		$this->view();
	}

	public function view() {
		$data['packages'] = $this->campaignPackages->getPackages();
		$this->layout->admin_home_view('admin/coupons_view_start', $data);
	}

	public function campaign($campaignID) {
        $this->load->library('form_validation');
        $this->load->helper('email_helper');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        $this->form_validation->set_rules('user_email', 'User Email ID', 'required|valid_email|callback_emailCheck');
        $this->form_validation->set_rules('recipient', 'Email to send coupon', 'valid_email');
        $this->form_validation->set_rules('discount', 'Discount', 'required|numeric');
        $this->form_validation->set_rules('couponCode', 'Coupon Code', 'required');
        
        if ($this->form_validation->run()) {
            $this->db->trans_begin();
            $campaign = $this->campaignPackages->getPackageByID($campaignID);
            $insertRecord = $this->couponCodes->insertCoupon(
                                $this->input->post('user_email'),
                                $this->input->post('recipient'),
                                $campaignID,
                                $this->input->post('couponCode'),
                                $this->input->post('discount')
                            );
            $recipient = '';
            $username = '';
            if ($this->input->post('recipient')) {
                $recipient = ($this->input->post('recipient'));
                $username = $recipient;
            } else {
                $recipient = ($this->input->post('user_email'));
                $user = $this->User->getUserByEmail($this->input->post('user_email'))->row();
                $username = $user->username;    
            }
            
            $sendEmail = email_helper('coupon_for_user', $recipient, array( 
                    'username' => $username,
                    'coupon' => $this->input->post('couponCode'),
                    'useremail' => $this->input->post('user_email'),
                    'campaignpackage' => $campaign->fans_no,
                    'discount_percentage' => $this->input->post('discount'),
                    'here' => 'http://google.com'
                ));
            
            if ($insertRecord && $sendEmail) {
                $this->load->library('fansurgeEmail');
                

                $data['success'] = '<div class="alert alert-success">Coupon successfully created</div>';    
            } else {
                $this->db->trans_rollback();
                $data['success'] = '<div class="alert alert-danger">Could not create coupon</div>';
            }
            $this->db->trans_commit();
        }
		$data['campaignID'] = $campaignID;
		$this->layout->admin_home_view('admin/coupons_view_campaign', $data);
	}

    public function emailCheck($email){

        $userQ = $this->User->getUserByEmail($email);
        $this->form_validation->set_message('emailCheck', 'The email address %s was not found');
        return $userQ->num_rows() > 0;
    }

    public function getTable($campaignID)
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('user_email', 'coupon_code', 'coupon_use_date', 'status', 'discount_percentage', 'created', 'id');
        
        // DB table to use
        $sTable = 'tbl_coupon_codes';
        //
    
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
        if(isset($sSearch) && !empty($sSearch))
        {
            for($i=0; $i<count($aColumns); $i++)
            {
                $bSearchable = $this->input->get_post('bSearchable_'.$i, true);
                
                // Individual column filtering
                if(isset($bSearchable) && $bSearchable == 'true')
                {
                    $this->db->or_like($aColumns[$i], $this->db->escape_like_str($sSearch));
                }
            }
        }
        
        // Select Data
        $this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $aColumns)), false);
        $rResult = $this->db->get_where($sTable, array('campaign_id' => $campaignID));
    
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
            'aaData' => array()
        );
        
        foreach($rResult->result_array() as $aRow)
        {
            $row = array();
            foreach($aColumns as $col)
            {
                $row[$col] = $aRow[$col];
                
            }
    
    
            $output['aaData'][] = $row;
        }
    
        echo json_encode($output);
    }


    public function generateCoupon() {
        $maxLength = 8;
        
        $couponCode = '';
        do {
            $couponCode = $this->generateRandomString($maxLength);
        } while ($this->couponCodes->exists($couponCode));
        
        echo json_encode(array('couponCode'=>$couponCode));
    }

    public function generateRandomString($length = 10) {
        $str = "abcdefghijklmnopqrstuvwxyz";
        $str = str_shuffle($str);
        $str .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $str = str_shuffle($str);
        $str = str_shuffle($str);
        if ($length > strlen($str)) {
            return false;
        }
        return substr(str_shuffle($str), 0, $length);
    }




}