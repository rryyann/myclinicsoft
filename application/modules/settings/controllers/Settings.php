<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Settings extends Secure {

	function __construct() {
        parent::__construct();
        
        $this->load->model('templates/Template');
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

		$this->template
			->title(get_class($this)) //$article->title
			->set_partial('header', 'include/header') //third param optional $data
			->set_partial('sidebar', 'include/sidebar') //third param optional $data
			->set_partial('ribbon', 'include/ribbon', $data) //third param optional $data
			->set_partial('footer', 'include/footer') //third param optional $data
			->set_partial('shortcut', 'include/shortcut') //third param optional $data
			->set_metadata('author', 'Randy Rebucas')
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('full-column') // application/views/layouts/two_col.php
			->build('manage', $data); // views/welcome_message
		
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
