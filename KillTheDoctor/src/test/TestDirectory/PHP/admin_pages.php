<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_Pages extends CI_Controller {

    public function __construct(){
        parent::__construct();

    }

    public function index()
    {
        $this->load->model('pages_model','pages',FALSE);
        $data['pages'] = $this->pages->getPages();
        $this->layout->admin_view('admin/pages' , $data);

    }
}
?>

