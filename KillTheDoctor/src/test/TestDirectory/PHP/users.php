<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Users extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if(!$this->authentication->is_logged_in()){
            redirect(site_url('admin'));
            die();
        }
    }

    public function index() {
        $this->all();
    }

	public function all() {
		$this->layout->admin_home_view('admin/user_listing');
	}

    public function getTable()
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
        $aColumns = array('first_name', 'last_name', 'username', 'email_address', 'created', 'status','id');
        
        // DB table to use
        $sTable = 'tbl_users';
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

    public function activate($userID) {
    	$this->load->model('user');
    	echo $this->user->setStatus($userID, 1);
    }

    public function deactivate($userID) {
    	$this->load->model('user');
    	echo $this->user->setStatus($userID, 0);
    }

    public function edit($userID) {
        $this->load->library('form_validation');
        $this->load->model('user');
        $this->load->model('cities');
        $this->load->model('countries');

  

        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        $this->form_validation->set_rules('firstName', 'First name', 'required|trim');
        $this->form_validation->set_rules('lastName', 'Last name', 'required|trim');
        $this->form_validation->set_rules('paymentEmail', 'Payment email', 'trim|valid_email');
        $this->form_validation->set_rules('dob', 'Date of birth', 'required|trim');
        $this->form_validation->set_rules('gender', 'Gender', 'required|trim');
        $this->form_validation->set_rules('phone', 'Phone', 'required|trim');
        $this->form_validation->set_rules('country', 'Country', 'required|trim');
        $this->form_validation->set_rules('city', 'City', 'required|trim');
        $this->form_validation->set_rules('status', 'Status', 'required');

        if ($this->form_validation->run()) {
            if ($this->startEdit($userID)) {
                $data['success']  = '<div class="alert alert-success">User updated</div>';
            } else {
                $data['success']  = '<div class="alert alert-danger">DB error</div>';
            }
        }

        $userProfile = $this->user->getUserProfile($userID);
        if ($userProfile == null) {
            show_404();
        }


        $data['user'] = $userProfile;
        $data['cities'] = $this->cities->getCitiesByCountryName($userProfile->country);
        $data['countries'] = $this->countries->getAllCountries();
        $this->layout->admin_home_view('admin/user_edit', $data);
        
    }

    private function startEdit($userID) {
        $this->load->model('user');

        $userProfile['first_name'] = $this->input->post('firstName');
        $userProfile['last_name'] = $this->input->post('lastName');
        //$userProfile['email_address'] = $this->input->post('emailAddress');
        $userProfile['payment_email'] = $this->input->post('paymentEmail');
        $userProfile['payment_method'] = $this->input->post('paymentMethod');
        $userProfile['dob'] = $this->input->post('dob');
        $userProfile['gender'] = $this->input->post('gender');
        $userProfile['phone'] = $this->input->post('phone');
        $userProfile['country'] = $this->input->post('country');
        $userProfile['city'] = $this->input->post('city');
        $userProfile['subscribe'] = $this->input->post('subscribe', 0);
        $userProfile['status'] = $this->input->post('status');

        $userProfile = (object)$userProfile;
        
        return $this->user->updateUserProfile($userID, $userProfile);
    }

    public function getCities() {
        $this->load->model('cities');
        $countryname = $this->input->post("country");
        $cities = $this->cities->getCitiesByCountryName($countryname);
        foreach ($cities as $row) {
            echo '<option value = "' . $row->name . '">' . htmlentities($row->name). '</option>';
        }
    }

    public function transactions($userID) {
        $data['userID'] = $userID;
        $this->layout->admin_home_view('admin/user_transactions', $data);
    }

    public function getTransactionTable($userID)
    {
        /* Array of database columns which should be read and sent back to DataTables. Use a space where
         * you want to insert a non-database field (for example a counter or static image)
         */
/*
SELECT 
    CASE tbl_users_payment_history.payment_type
        WHEN 'Earn' THEN tbl_users_payment_history.amount
        ELSE 0.000
    END AS earn_amount,
    CASE tbl_users_payment_history.payment_type
        WHEN 'Pay' THEN tbl_users_payment_history.amount
        ELSE 0.000
    END AS paid_amount,
    tbl_user_campaigns.page_title,
    tbl_users_payment_history.comment, 
    tbl_users_payment_history.payment_status,
    tbl_users_payment_history.created,
FROM tbl_users_payment_history 
JOIN tbl_user_campaigns
ON tbl_users_payment_history.user_campaign_id = tbl_user_campaigns.id
WHERE tbl_users_payment_history.user_id = 29
*/

        $aColumns = array(
            "CASE tbl_users_payment_history.payment_type
                WHEN 'Earn' THEN tbl_users_payment_history.amount
                ELSE 0.000
             END AS earn_amount",
            "CASE tbl_users_payment_history.payment_type
                WHEN 'Pay' THEN tbl_users_payment_history.amount
                ELSE 0.000
              END AS paid_amount",
             'tbl_user_campaigns.page_title',
             'tbl_users_payment_history.comment',
             'tbl_users_payment_history.payment_status',
             'tbl_users_payment_history.created',
            );

        $columnNames = array(
            'earn_amount', 
            'paid_amount', 
            'page_title', 
            'comment', 
            'payment_status', 
            'created');
        // DB table to use
        $sTable = 'tbl_users_payment_history';
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
                    //$this->db->order_by($aColumns[intval($this->db->escape_str($iSortCol))], $this->db->escape_str($sSortDir));
                    //$this->db->order_by($iSortCol, $this->db->escape_str($sSortDir));
                    $dir = $this->db->escape_str($sSortDir);
                    $iSortCol++;
                    $this->db->_protect_identifiers = FALSE;
                    $this->db->order_by("$iSortCol $dir");

                    //$this->db->order_by('field (5, 1, 3, 2, 4)', NULL, FALSE);
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

                $bSearchable = $this->input->get_post('bSearchable_'.$i, true);
                
                // Individual column filtering
                if(isset($bSearchable) && $bSearchable == 'true')
                {
                    $this->db->or_like('page_title', $this->db->escape_like_str($sSearch));
                    $this->db->or_like('comment', $this->db->escape_like_str($sSearch));
                    $this->db->or_like('payment_status', $this->db->escape_like_str($sSearch));
                    $this->db->or_like('created', $this->db->escape_like_str($sSearch));
                    $this->db->or_like('amount', $this->db->escape_like_str($sSearch));

                }

        }
        
        // Select Data
        $this->db->select('SQL_CALC_FOUND_ROWS '.str_replace(' , ', ' ', implode(', ', $aColumns)), false);
        $this->db->join('tbl_user_campaigns', 'tbl_user_campaigns.id = tbl_users_payment_history.user_campaign_id');
        $rResult = $this->db->get_where($sTable, array('tbl_users_payment_history.user_id' => $userID));
    
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
            foreach($columnNames as $col)
            {
                $row[$col] = $aRow[$col];
            }
    
    
            $output['aaData'][] = $row;
        }
    
        echo json_encode($output);
    }

    public function email() {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
        $this->load->model('user');

        $data['users'] = $this->user->getAllEmails();
        $this->form_validation->set_rules('recipients', 'Recipients', 'required|trim');
        $this->form_validation->set_rules('message', 'Message', 'required|trim|min_length[1]');
        if ($this->input->post('recipients') != "all") {
            $this->form_validation->set_rules('multiRecipients', 'Recepients list', 'required');
                
        }
        $this->form_validation->set_rules('subject', 'Subject', 'required|trim');
        if ($this->form_validation->run()) {
            $this->load->library('FansurgeEmail');
            $this->load->helper('email');
            if ($this->input->post('recipients') == "all") {
                foreach ($data['users'] as $user) {
                    if (valid_email($user->email_address)) {
                        $this->fansurgeemail->addRecipient($user->email_address);
                    }
                }
            } else {
                $recipients = $this->input->post('multiRecipients');
                foreach ($recipients as $email){
                    $this->fansurgeemail->addRecipient($email);
                }
            }

            if ($this->fansurgeemail->sendEmail($this->input->post('subject'), $this->input->post('message'))) {
                $data['success']  = '<div class="alert alert-success">Email Sent</div>';
            } else {
                $data['success']  = '<div class="alert alert-danger">Unable to send email</div>';
            }           
        }
        
        $this->layout->admin_home_view('admin/user_email', $data);
    }
}