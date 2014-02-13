<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Home extends CI_Controller {

    public function __construct(){
        parent::__construct();
       
    }

	public function index()
	{
		$this->authenticate() ? $this->layout->admin_view('admin/home') : 
		$this->layout->admin_view('admin/admin_home_view');
    }
