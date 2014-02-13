<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model('admin_model','admin');
    }

	public function index()
	{
        $this->authentication->admin_redirects();
    }

    public function login()
    {   
        if(!$this->input->post())
        {
            $this->authentication->admin_redirects();
    
        }
        else
        {
            $this->load->helper(array('form', 'url'));
            $this->load->library('form_validation');

            $data = array();
            if(!$this->form_validation->run('login'))
            {
                $this->form_validation->set_error_delimiters('<div class="font_red">', '</div>');
                $this->layout->admin_view('admin/login_view',$data);
            } 
            else
            {
                if($this->authenticate()){
                     $this->authentication->admin_redirects();
                } 
                else {
                    $data['error_msg']="Please enter a correct user name or password";
                    $this->layout->admin_view('admin/login_view',$data);
                }
                    
            }    
        }        
        
        
    }
    /*
        checks if the user is logged in or not
    */
    public function authenticate()
    {
        $strUsername = $this->input->post('username');
        $strPassword = $this->input->post('password');
        
        $arrUser = $this->admin->getUser($strUsername, $strPassword);
    
        if(count($arrUser) > 0){
            $arrUser['boolIsLoggedIn'] = true;
            $this->session->set_userdata('admin_user',$arrUser);
        }
        return (count($arrUser) > 0) ? true : false ;
    }

    public function logout()
    {
        $this->session->unset_userdata('admin_user');
        $this->authentication->admin_redirects();
    }

}