<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Settings extends Secure {

	function __construct() {
        parent::__construct();

        $this->load->library('location_lib');

        $this->load->language('setting', 'english');
        $this->load->language('common/common', 'english');
    }

    function _remap($method, $params = array()) {
 
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        $directory = getcwd();
        $class_name = get_class($this);
        $this->display_error_log($directory,$class_name,$method);
    }

    private function _init($data)
	{

		$this->layout
			->title(get_class($this)) 
			->set_partial('header', 'include/header') 
			->set_partial('sidebar', 'include/sidebar') 
			->set_partial('ribbon', 'include/ribbon', $data) 
			->set_partial('footer', 'include/footer') 
			->set_partial('shortcut', 'include/shortcut') 
			->set_metadata('author', 'Randy Rebucas')
			->set_layout('full-column') 
			->build('manage', $data); 
		
	}

	function index()
	{
		$this->load->model('templates/Template');

		$data['module'] = get_class($this); 
		
		if ($this->input->is_ajax_request()) 
		{
			$this->load->view('manage', $data);
        } 
		else
		{
			$this->_init($data);
		}
	}

	function my_profile($enc_id){
		
		$id = url_base64_decode($enc_id);

		$data['module'] = $this->lang->line('common_my_profile');

		if ($this->input->is_ajax_request()) 
		{
			$this->load->model('user/User_model');
			
			$data['info'] = $this->User_model->get_profile_info($id);
			$this->load->view('user/profile', $data);
        } 
		else
		{
			$this->_init($data);
		}
	}
	
	function encryptID($user_id){

		redirect('my-profile/'.url_base64_encode($user_id));

	}
}
