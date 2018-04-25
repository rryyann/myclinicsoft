<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

/*
 * MyClinicSoft
 * 
 * A web based clinical system
 *
 * @package		MyClinicSoft
 * @author		Randy Rebucas
 * @copyright	Copyright (c) 2016 - 2018 MyClinicSoft, LLC
 * @license		http://www.myclinicsoft.com/license.txt
 * @link		http://www.myclinicsoft.com
 * 
 */

class Errors extends Secure {

	function __construct() 
	{

        parent::__construct();
    }

    function _remap($method, $params = array()) 
    {
 
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        $this->display_error_log(getcwd(), get_class($this), $method);
    }

    private function _init($data)
	{
		$data['heading'] = '404 Page Not Found';
		$data['message'] = 'The page you requested was not found.';

		$this->layout
			->title(get_class($this)) 
			->set_partial('header', 'include/header') 
			->set_partial('sidebar', 'include/sidebar', $data) 
			->set_partial('ribbon', 'include/ribbon', $data) 
			->set_partial('footer', 'include/footer') 
			->set_partial('shortcut', 'include/shortcut') 
			->set_metadata('author', 'Randy Rebucas')
			->set_layout('full-column') 
			->build('manage'); 
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
