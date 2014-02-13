<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_pageupdate extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

    public function index()
    {
        $this->load->model('pageupdate_model','pageupdate',FALSE);
        $data['pageupdate'] = $this->pages->getPages();
        $this->layout->admin_view('admin/pageupdate' , $data);

    }
}
?>

