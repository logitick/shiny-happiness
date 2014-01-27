<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	class Campaign extends CI_Controller {

		public function __construct(){
			parent::__construct();
			$this->load->model('campaign_model','campaign');
			$this->load->model('category_model','category');
		}

		public function index()
		{
			$this->campaign_listing();
		}

		public function campaign_listing(){
			$data = '';
			$this->layout->admin_home_view('admin/campaign_listing' , $data);
		}



		public function getCampaignsAjax()
		{
			$data = $this->input->post();

			$arrCampaigns = $this->campaign->getAllCampaigns($data['iDisplayStart'],$data['iDisplayLength'],$data['sSearch']);
				
			$new_arr = $arrCampaigns;
			$iTotalCampaigns = $this->campaign->countAllCampaigns();
			$display =  array('sEcho' => $data['sEcho'],
							  'iTotalDisplayRecords'=> $iTotalCampaigns,
							  'iTotalRecords'=>$iTotalCampaigns,
							  'aaData' => $new_arr->result_array()
						);
			
			foreach($arrCampaigns->result() as $key => $value){
				$display['aaData'][$key]['name'] = $this->interest_callback($value->name) . "<br/><br/>" . $value->page_keywords ; 
			}
			
			echo json_encode($display);
		}
		#jquery javascript independent
		public function add_campaign(){
			$data['categories'] = $this->category->getAllCategories();
			$data['packages'] = $this->campaign->getAllCampaignPackages();
			
			$this->load->library('form_validation');
			
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			
			if($this->form_validation->run('form_add_campaign_0123') == FALSE){
				
				$this->layout->admin_home_view('admin/add_campaign',$data);
			}else{
				$this->register_campaign();
				$data['message'] = '<div class = "alert alert-success"> Registration Success! </div>';
				$this->layout->admin_home_view('admin/add_campaign',$data);
			}
			
			
		}
		
				
		#edit
		#queries one by one table
		public function edit_campaign($id){
			gc_collect_cycles( );
			#stripping % problem
			$id = str_replace('%20' , '' , $id);
			
			if(empty($id) || !is_numeric($id)){
				show_404();
			}
			#set the id
			$data['id'] = $id;
		
			#load the form validation
			$this->load->library('form_validation');
			#load the campaigns
			$this->load->model('table_user_campaigns','campaigns',FALSE);
			#load the campaign category
			$this->load->model('table_campaign_category','category' , FALSE);
			#load the categories
			$this->load->model('table_categories' , 'categories' , FALSE);
			#load the packages model
			$this->load->model('table_campaign_packages','packages',FALSE);
			#helper
			$this->load->helper('html');
			
			if($this->campaigns->select(-1 , -1 , array('id', $id))->num_rows() <= 0){
				show_404();
			}
			
			
			$sql = 'SELECT campaign.id, globals.name , users.username,campaign.user_id, campaign.target_fans, campaign.page_description,campaign.site_fans ,campaign.page_title , campaign.social_page_link,
					campaign.geographical_preferrence, campaign.page_keywords, campaign.campaign_image, campaign.campaign_package_id,campaign.is_adult
					FROM tbl_user_campaigns campaign 
					left join tbl_users users 
					on users.id = campaign.user_id 
					left join tbl_global_campaigns globals
					on globals.id = campaign.global_campaign_id 
					where campaign.id = ?';
			
			
			
			$query = $this->campaigns->queryString($sql , array($id))->result_array();
			$query_packages = $this->packages->select(-1 , -1 , null);
			
			$sql = 'SELECT categories.name FROM `tbl_campaign_category` category
					right join `tbl_categories` categories
					on category.category_id = categories.id
					where user_campaign_id = ?';
			$category_categories = $this->categories->queryString($sql , $id);
		
			
	
			$new_category = array();
			foreach($category_categories->result() as $package){
				$new_category[]= $package->name;
			}
			
			$data['image_properties'] = array(
			  'src' => CAMPAIGN_IMAGE_PATH. $query[0]['campaign_image'],
			  'alt' => 'thumbnail',
			  'id' => 'thumbnail_image',
			  'class' => 'thumbnail',
			  'width' => '100',
			  'height' => '100'
			);

			$data['campaign_id'] = $query[0]['id'];
			$data['user_id'] = $query[0]['user_id'];
			$data['package_id'] = $query[0]['campaign_package_id'];
			$data['action_url'] = base_url() . 'admin/campaign/edit_campaign/' . $id;
			$data['user'] =$query[0]['username'];
			$data['packages'] = $query_packages;
			$data['categories'] = implode(' And ',$new_category);
			$data['description'] = $query[0]['page_description'];
			$data['target_fans'] = $query[0]['target_fans'];
			$data['fans'] = $query[0]['site_fans'];
			$data['title'] = $query[0]['page_title'];
			$data['link'] = $query[0]['social_page_link'];
			$data['is_adult'] =$query[0]['is_adult'];
			$data['keywords'] = $query[0]['page_keywords'];
			$data['preferences'] = $query[0]['geographical_preferrence'];
			$data['image'] = $query[0]['campaign_image'];
			$this->form_validation->set_error_delimiters('<div class="alert alert-danger">', '</div>');
			
			if($this->form_validation->run('form_admin_edit') === FALSE){
				$this->layout->admin_home_view('admin/edit_campaign' , $data);
			}else{
				$this->edit($data);
				$data['message'] = '<div class="alert alert-success"> Edit Successful, If data havent changed, just refresh. </div>';
				$this->layout->admin_home_view('admin/edit_campaign' , $data);
			}
		}
		
		public function edit($data){
			if($this->input->post('submit')){
				# files if set and image is there and no errors
				if(isset($_FILES) && $_FILES['image'] && $_FILES['image']['error'] == 0){
					#the same filename used by the previous image
					$filename = $data['image'];
					
					#get the image path + previous filename
					$path = CAMPAIGN_IMAGE_PATH . $filename;
					
					#if file already exists then we must replace it
					if(file_exists($path)) unlink($path);
					
					move_uploaded_file($_FILES['image']['tmp_name'] , $path);
				}
				#load the model
				$this->load->model('table_user_campaigns','campaigns',FALSE);
				#load payment history
				$this->load->model('table_users_payment_history','payment',FALSE);
				#load packages
				$this->load->model('table_campaign_packages','packages',FALSE);
				
				#implode the array preference
				$preference = implode(',',$this->input->post('preference'));
				$pageRadio = $this->input->post('adult_content');
				
				#select the package for the new update
				$select_new_package = $this->packages->select(-1 , -1 , array('id' , $this->input->post('package') ))->row();
				
				
				
				#set the update data for user campaigns
				$update_campaign_data = array(
					'page_description' => $this->input->post('description'),
					'no_of_fans_at_start' => $this->input->post('fans'),
					'page_title' => $this->input->post('title'),
					'social_page_link' => $this->input->post('link'),
					'target_fans' => $data['target_fans'] + ($select_new_package->fans_no * $select_new_package->cost_per_fan),
					'campaign_package_id' => $this->input->post('package'),
					'is_adult' => $pageRadio[0],
					'geographical_preferrence' => $preference,
					'page_keywords' => $this->input->post('keywords')
					
				);
				

				#start transaction
				$this->campaigns->start();

				#update the table
				$this->campaigns->update(array('key' => 'id', 'value' => $data['id'] ) , $update_campaign_data);
				
				#test query to payment history
				$test_query = $this->payment->select(-1 , -1 , array('user_id' , $data['user_id'] ,'user_campaign_id', $data['campaign_id']));
				
				#query package from user campaign id
				$query_package = $this->packages->select(-1 , -1 , array('id',$data['package_id']))->row();
				
	
				#check if has history of payment
				if($test_query->num_rows() > 0){
					#get history of payment
					$payment_history = $test_query->row();
					#get amount form payment history and append new package
					$amount = $payment_history->amount + ($query_package->fans_no * $query_package->cost_per_fan);
					
					$update_payment_history = array(
						'payment_status' => 'Confirm',
						'amount' => $amount
					);
					
					$this->payment->update(array('key' => 'id' , 'value' => $payment_history->id) , $update_payment_history);
				
				#if no payment history then we have to create
				#a new payment history for this user and usercampaignid
				}else{
					$selected_package = $this->packages->select(-1 ,-1,array('id',$_POST['package']))->row();

					$insert_history = array(
						'id' => null,
						'user_id' => $data['user_id'],
						'amount' => ($selected_package->fans_no * $selected_package->cost_per_fan),
						'user_campaign_id' => $data['campaign_id'],
						'comment' => 'Campaign created by admin using package ' . $selected_package->name,
						'payment_status' => 'Confirm',
						'payment_type' => 'Pay',
						'alert_status' => 1,
						'coupon_code' => '',
						'discount_percentage' => 0.00,
						'online_payment' => 0.00
					);
					
					$this->payment->insert($insert_history);
					
				}
				
				#complete transaction
				$this->campaigns->complete();
				
			}
		}
		
		/*
		* 
		* 
		* 
		* 
		*/
		
		public function register_campaign(){
			if($this->input->post('submit')){
				$global_campaign_id = -1;
				#load the table users to get the id 
				$this->load->model('table_users_model','users',FALSE);
				#load the categories table
				$this->load->model('table_campaign_category','campaign_category',FALSE);
				#load the campaign packages table
				$this->load->model('table_campaign_packages' , 'packages', FALSE);
				#load the user_campaigns table
				$this->load->model('table_user_campaigns' , 'user_campaigns' , FALSE);
				#load the payment history table
				$this->load->model('table_users_payment_history' , 'payment_history' , FALSE);
				#get email
				$email = $this->input->post('email');
				#query
				$query_users = $this->users->select(-1 , -1 , array('email_address',$email))->row();
				
				#id
				$id = $query_users->id;
				#array of packages
				$package = $this->input->post('package');
				#array of categories
				$categories = $this->input->post('categories');
				#description
				$description = $this->input->post('description');
				#fans
				$fans = $this->input->post('fans');
				#title 
				$title = $this->input->post('title');
				#link
				$link = $this->input->post('link');
				#adult_content
				$adult_content = $this->input->post('adult_content');
				#preference array
				$preferences = $this->input->post('preference');
				#keywords array
				$keywords = $this->input->post('keywords');
				#image
				#$image_file = $_FILES['image'];
				/*
				$filename = $id . '-' . uniqid() . '-' . $_FILES['image']['name'];
				$path = CAMPAIGN_IMAGE_PATH . $filename;
				#move uploaded_file from temp storage to path storage
				move_uploaded_file($_FILES['image']['tmp_name'] , $path);
				*/
				$param0 = array(
					0 => array(
						'width' => 500,
						'height' => 500,
						'filename' => $id,
						'path_directory' => CAMPAIGN_IMAGE_PATH,
						'state' => 'upload'
						),
					1 => array(
						'width' => 100,
						'height' => 100,
						'filename' => $id,
						'path_directory' => CAMPAIGN_IMAGE_THUMBNAIL_PATH,
						'state' => 'upload'
					)
				);
				
				#set config
				$config = array(
				 $param0,
				 $_FILES);
				#load library
				$this->load->library('imageuploader' , $config);
				#generate images
				$this->imageuploader->upload_files();
				#get image file name
				$filename = $this->imageuploader->getFileName();
				
				
				
				#init http
				$http = array();
				
				$this->load->model('table_user_campaigns' , 'user_campaign' , FALSE);
				#retrieve data selecting from youtube , facebook , twitter
				if(strpos(strtolower($link) , 'facebook') == TRUE){
					#return the http json encoded data
					$http =  @$this->getFLikes($link);
					#set the global data to facebok id
					$global_campaign_id = 1;
					
				}elseif(strpos(strtolower($link) , 'twitter') == TRUE){
					#return the http json encoded data
					$http =  @$this->getTCount($link);
					#set the global data to twitter id
					$global_campaign_id = 2;
				}elseif(strpos(strtolower($link) , 'youtube') == TRUE){
					#return the http json encoded data
					$http = @$this->getYLikes($link);
					#set the global data to youtube id
					$global_campaign_id = 3;
				} 
				
				$target_fans = $this->packages->select(-1 , -1 , array('id',$package))->row();

				$preferences = implode(',' , $preferences);
				#getting slug
				$slug = str_replace(array(' ', '.') , '-' , strtolower($http['name'])) . '-' . time();
				#set the insert data
				$user_campaign_data = array(
					'id' => null,
					'global_campaign_id' => $global_campaign_id,
					'slug' => $slug,
					'user_id' => $id,
					'page_title' => $http['name'],
					'page_id' => $http['id'],
					'campaign_package_id' => $package,
					'page_description' => $description,
					'no_of_fans_at_start' => $http['likes'],
					'target_fans' => $target_fans->fans_no,
					'site_fans' => 0,
					'campaign_image' => $filename,
					'social_page_link' => $link,
					'is_adult' => $adult_content,
					'geographical_preferrence' => $preferences,
					'page_keywords' => $keywords,
					'status' => 0,
					'lastUserId' => 0,
					'last_email_sent_id' => 0,
					'alert_mail_status' => 1
				);
				#start transaction
				$this->user_campaigns->start();
				
				#insert user campaigns
				$this->user_campaigns->insert($user_campaign_data);
				
				#get the last campaign inserted id
				$last_campaign_id = $this->user_campaigns->getLastInsertedID();
				
				#get the campaign package
				$campaign_packages = $this->packages->select(-1 , -1 , array('id' , $package))->row();
				
				/*
				* looping through all categories
				*/
				for($i = 0 ; $i < count($categories) ; $i++){
					#store data for insertion in campaign category
					$campaign_category_data = array(
						'id' => null,
						'user_campaign_id' => $last_campaign_id,
						'category_id' => $categories[$i]
					);
					
					#insert data to campaign category
					$this->campaign_category->insert($campaign_category_data);
				}			
				
				#set the history data with/o cc
				$history_data = array(
					'id' => null,
					'user_id' => $id,
					'amount' => $campaign_packages->fans_no * $campaign_packages->cost_per_fan,
					'user_campaign_id' => $last_campaign_id,
					'comment' => 'Campaign is created by admin',
					'payment_status' => 'Confirm',
					'payment_type' => 'Pay',
					'alert_status' => 1,
					'coupon_code' => '' ,
					'discount_percentage' => 100,
					'online_payment' => 0
				);
				#insert into history table
				$this->payment_history->insert($history_data);
				
				#get last payment history id
				$last_payment_id = $this->payment_history->getLastInsertedID();
				
				#close transaction
				$this->user_campaigns->complete();
			}
		}
	

		#ajax 
		public function activate(){
			if($this->input->is_ajax_request()){
				$this->load->model('table_user_campaigns','campaigns', FALSE);
				$id = $this->input->post('id');
				$update_campaign_data = array(
					'status' => 1
				);
				
				#start transaction
				$this->campaigns->start();
				
				#update
				$this->campaigns->update(array('key' => 'id' , 'value' => $id) , $update_campaign_data);
			
				#complete transaction
				$this->campaigns->complete();
				
				
				echo json_encode(array('message' =>  'success' ,'text' => 'deactivate'));
			}	
		}
	
		#ajax
		public function deactivate(){
			if($this->input->is_ajax_request()){
				$this->load->model('table_user_campaigns','campaigns', FALSE);
				$id = $this->input->post('id');
				$update_campaign_data = array(
					'status' => 0
				);
				
				#start transaction
				$this->campaigns->start();
				
				#update
				$this->campaigns->update(array('key' => 'id' , 'value' => $id) , $update_campaign_data);
			
				#complete transaction
				$this->campaigns->complete();
				
				echo json_encode(array('message' =>  'success' ,'text' => 'activate'));
			}
		}


		#ajax call
		public function interest_callback($id){
				$this->load->model('table_campaign_category','ccategory',FALSE);

				
				$sql = 'select categories.name from tbl_campaign_category category
						left join tbl_categories categories
						on category.category_id = categories.id 
						where category.user_campaign_id = ?';
				
				$interests = $this->ccategory->queryString($sql ,$id);
				$business = array();
				foreach($interests->result() as $data){
					$business[]= $data->name;
				}
				$business = implode(' And ' , $business);
				return $business;
			
			
		}
	

	
		#ajax validation
		public function check_non_unique_email(){
			if($this->input->is_ajax_request()){
				$this->load->model('table_users_model','users',FALSE);
				
				$email = $_POST['email'];
				
				$query = $this->users->select(-1 , -1 , array('email_address' , $email));
				
				if($query->num_rows() > 0)
					echo 'true';
				else echo 'false';
			}
		}
		
		#ajax check existing link
		public function existing_link(){
			if($this->input->is_ajax_request()){
				
			}
		}
		
		#used for edit
		#form validation callback
		public function check_package($str){
			if($str == 'Select-Package'){
				$this->form_validation->set_message('check_package' , 'Please select the correct package');
				return FALSE;
			}
			return TRUE;
		}
		
		#form validation callback
		public function check_existing_email($str){
				$this->load->model('table_users_model','users',FALSE);
				
				$email = $str;
				
				$query = $this->users->select(-1 , -1 , array('email_address' , $email));
				
				if($query->num_rows() <= 0){
					$this->form_validation->set_message('check_existing_email' , 'The email you input was not found in our database.');
					return FALSE;					
				}
				
			return TRUE;
				
		}
		
		#check image when processing updating a campaign
		public function image_update_check(){
			if(isset($_FILES['image'])){
				return TRUE;
			}else $this->image_check();
		}
		
		#check image when processing registering a campaign, 
		public function image_check(){
			$allowedExts = array("gif", "jpeg", "jpg", "png");
			$temp = explode(".", $_FILES["image"]["name"]);
			$extension = end($temp);
			if(!in_array($extension , $allowedExts) ){
				$this->form_validation->set_message('image_check' , 'The image extension is not a valid image');
				return FALSE;
			}else if($_FILES["image"]["error"] > 0){
				$this->form_validation->set_message('image_check' , 'The image file error occured. Please check the file again if it is image.');
				return FALSE;
			}else if($_FILES["image"]["size"] > 5242880){
				$this->form_validation->set_message('image_check' , 'The image file size is bigger than the max file size.');
				return FALSE;
			}
			return true;
				
		}
		

		
	
		#form validation callback checking url
		public function url_checking($str){
			$pattern = "/^(http|https):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i";
		
			$http = array();
		 
			if(strpos(strtolower($str) , 'facebook') == TRUE){
				#return the http json encoded data
				$http =  $this->getFLikes($str);
				
			}elseif(strpos(strtolower($str) , 'twitter') == TRUE){
				#return the http json encoded data
				$http =  $this->getTCount($str);
			}elseif(strpos(strtolower($str) , 'youtube') == TRUE){
				#return the http json encoded data
				$http = $this->getYLikes($str);
			} 
	
			//$link_test = @file_get_contents($str);
			if(!strpos(@$http['header'] , "200")){
				$this->form_validation->set_message('url_checking' , 'The page link is not a valid URL header');
				return FALSE;
			
			}elseif(!isset($http['name']) || $http['name'] == ''){
				$this->form_validation->set_message('url_checking' , 'The page link is not a valid link or is not a valid facebook/youtube/twitter page.');
				return FALSE;			
			
			}elseif(!isset($http['likes']) || $http['likes'] == ''){
				$this->form_validation->set_message('url_checking' , 'The page link is not a valid link or is not a valid facebook/youtube/twitter page.');
				return FALSE;		 
			}
			elseif (!preg_match($pattern, $str)){
				$this->form_validation->set_message('url_checking' , 'The page link is not a valid URL' );
				return FALSE;
			}
			 
         return TRUE;
		}
		


		#ajax request to check for url to exist in add_campaign
		public function url_non_exist(){
			if($this->input->is_ajax_request()){
				$this->load->model('table_user_campaigns','campaigns',FALSE);
				$link = $_POST['link'];
				$query = $this->campaigns->select(-1 , -1  , array('social_page_link' , $link));
				
				#get the response header
				$http = @file_get_contents($link);
				
				#checking if response header is ok
				if(strpos( @$http_response_header[0], '200' ) == TRUE){
					echo 'true';
				}
				elseif($query->num_rows() <= 0){
					echo 'true';
				}else echo 'false';
			}
		}
		
		public function getFLikes($link){
			#simple decode
			$http = json_decode(@file_get_contents(str_replace('www','graph',$link )) , true);
			
			#add header
			$http['header'] = @$http_response_header[0];
			
			
			return $http;
		}
		
		public function getTCount($link){
			$twitter = 'https://cdn.api.twitter.com/1/users/lookup.json?screen_name=';
			
			$http = array();
			
			$id = explode('/' ,$link);
			
			$newLink = $twitter . '' . $id[3];
			
			$http = json_decode(@file_get_contents($newLink ) , true);
			
			
			#check if follwers count is there
			if(isset($http[0]['followers_count'])){
				#add header
				$http['header'] = @$http_response_header[0];
				#add likes
				$http['likes'] = @$http[0]['followers_count']; 
				#add page title
				$http['name'] = @$http[0]['screen_name'];
				#add page id
				$http['id'] = @$http[0]['id'];
				return $http;
			}else
				return $http;
			
		}
		
		public function getYLikes($link){
			$youtube = 'http://gdata.youtube.com/feeds/api/videos/';
			$last_link = '?v=2&alt=json';
			
			$http = array();
			
			$video_id = explode('=', $link);
			
			$newLink = $youtube . '' . $video_id[1] . '' . $last_link;
			$http = json_decode(@file_get_contents($newLink ) , true);
			
			#if entry exist then well put likes in it
			if(isset($http['entry'])){
				#add header
				$http['header'] = @$http_response_header[0];
				#add likes
				$http['likes'] = @$http['entry']['yt$rating']['numLikes']; 
				#add page title
				$http['name'] = @$http['entry']['title']['$t']; 
				#add page id
				$http['id'] = @$video_id[1];
				return $http;
			#else just return it 
			}else
				return $http;
			
			
		}

	
	/*
	* end of registration and trappings
	*
	*
	*/

		
	}