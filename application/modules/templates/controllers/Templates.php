<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Templates extends Secure {

	function __construct() {
        parent::__construct();
        $this->load->model('Template');
    }

    function _remap($method, $params = array()) {
 
        if (method_exists($this, $method)) {
            return call_user_func_array(array($this, $method), $params);
        }

        $directory = getcwd();
        $class_name = get_class($this);
        $this->display_error_log($directory,$class_name,$method);
    }

    private function _init()
	{
		
		$this->template
			->title(get_class($this)) //$article->title

			->set_partial('header', 'include/header') //third param optional $data
			->set_partial('sidebar', 'include/sidebar') //third param optional $data
			->set_partial('ribbon', 'include/ribbon') //third param optional $data
			->set_partial('footer', 'include/footer') //third param optional $data
			->set_partial('shortcut', 'include/shortcut') //third param optional $data
			->set_metadata('author', 'Randy Rebucas')
			// application/views/some_folder/header
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('full-column') // application/views/layouts/two_col.php
			->build('manage'); // views/welcome_message);
		
	}

	function index()
	{
		$data['module'] = 'Templates';
		if ($this->input->is_ajax_request()) 
		{
			
			$this->load->view('manage', $data);
        } 
		else
		{
			$this->_init($data);
			
		}
	}
	
	function load_ajax() {
	
		if ($this->input->is_ajax_request()) 
		{	
			$this->load->library('datatables');
	       
	        $this->datatables->select("tid as id, tname as name, ttype as type, tstatus as status, tcreated as created, license_key as license", false);
	        
			$this->datatables->where_in('license_key', array($this->license_id, 'system'));
	        
	        $this->datatables->from('templates');

	        echo $this->datatables->generate('json', 'UTF-8');
    	}else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
	function view($id = -1){

        if ($this->input->is_ajax_request()) 
		{
			
			$data['info'] = $this->Template->get_info($id);
			
			$templates = array('' => 'Select');
			// $array = array($this->license_id, 'system');

			// foreach ($this->Template->get_all($array)->result_array() as $row) {
			// 	$templates[$row['tid']] = $row['tname'];
			// }

			$data['templates'] = $templates;
			
	        $this->load->view("form", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
	function preset($id = 0){
		if ($id) {

            $preset = $this->Template->load($id);

            echo html_entity_decode(html_entity_decode($preset->tcontent));
        } else {
            echo '';
        }
	}
	function doSave($id = -1){
		
		$template_data = array(
			'tname'			=>$this->input->post('name'),
			'tcontent'		=>$this->input->post('content'),
			'ttype'			=>$this->input->post('types'),
			'tstatus'		=>1,//$this->input->post('status') ? 1 : 0,
			'license_key'	=>$this->license_id
		);
		
		if($this->Template->save($template_data, $id))
		{
			if($id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$template_data['tname']));
			}
			else 
			{
				echo json_encode(array('success'=>true,'message'=>$template_data['tname']));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$template_data['tname']));
		}
			
	}
	
	function details($id = -1){
    	if ($this->input->is_ajax_request()) 
		{
	    	$data['info'] = $this->Template->get_info($id);
	        $this->load->view("detail", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
    function delete($id){

    	if ($res = $this->Template->delete($id)) {
			echo json_encode(array('success' => true, 'message' => 'Template successfully deletd!'));
		} else {
			echo json_encode(array('success' => false, 'message' => $res ));
		}

    }

}
