<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Dashboard extends Secure {

	function __construct() {
        parent::__construct();
        $this->load->language('dashboard', 'english');
        $this->load->language('posts/posts', 'english');
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
			// application/views/some_folder/header
			->set_partial('header', 'include/header') //third param optional $data
			->set_partial('sidebar', 'include/sidebar', $data) //third param optional $data
			->set_partial('ribbon', 'include/ribbon', $data) //third param optional $data
			->set_partial('footer', 'include/footer') //third param optional $data
			->set_partial('shortcut', 'include/shortcut') //third param optional $data
			->set_metadata('author', 'Randy Rebucas')
			// application/views/some_folder/header
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('full-column') // application/views/layouts/two_col.php
			->build('manage'); // views/welcome_message
	}

	function index()
	{
		$data['module'] = get_class();

		if ($this->input->is_ajax_request()) 
		{
			$this->load->view('manage', $data);
        } 
		else
		{
			$this->_init($data);
		}
		
	}

}
