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

class Custom_Fields extends Secure {
	
	public function __construct()
	{
		parent::__construct();
		
		$this->load->model('Custom_field');
	}
	
	function _remap($method, $params = array()) {
 
        if (method_exists($this, $method)) 
        {
            return call_user_func_array(array($this, $method), $params);
        }

        $this->display_error_log(getcwd(), get_class($this), $method);
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

	function load_ajax() {
	
		// if ($this->input->is_ajax_request()) 
		// {	
			$this->load->library('datatables');
	       
	        $this->datatables->select("custom_field_id as id, custom_field_table as tbl, custom_field_label as lbl, license_key as license", false);
	        
			$this->datatables->where('license_key',$this->license_id);
	        
	        $this->datatables->from('custom_fields');

	        echo $this->datatables->generate('json', 'UTF-8');
	        
    	// }else{

	    // 	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
     //        redirect(strtolower(get_class()));
	    // }
    }

    function doSave($id = -1){
		
		$custom_data = array(
			'custom_field_table'		=>$this->input->post('custom_field_table'),
			'custom_field_label'		=>$this->input->post('custom_field_label'),
			'custom_field_type'	=>$this->input->post('custom_field_type'),
			'custom_field_sort'	=>$this->input->post('custom_field_sort'),
			'license_key'	=>$this->license_id
		);
		
		if($this->Custom_field->save($custom_data, $this->license_id, $id))
		{
			if($id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$custom_data['custom_field_table']));
			}
			else 
			{
				echo json_encode(array('success'=>true,'message'=>$custom_data['custom_field_table']));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$custom_data['custom_field_table']));
		}
			
	}

	function details($id = -1){
    	if ($this->input->is_ajax_request()) 
		{
	    	$data['info'] = $this->Role->get_info($id);
	        $this->load->view("detail", $data);

	    }else{

	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');

            redirect(strtolower(get_class())); 
	    }
    }

	function view($id = -1)
	{

        if ($this->input->is_ajax_request()) 
		{

			$data['info'] = $this->Custom_field->get_info($id);

	        $this->load->view("form", $data);
			
	    }
	    else
	    {

	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect(strtolower(get_class()));

	    }
    }

	function delete($id)
	{

    	if ($res = $this->Custom_field->delete($id)) 
    	{
			echo json_encode(array('success' => true, 'message' => 'Custom fiels successfully deletd!'));
		} 
		else 
		{
			echo json_encode(array('success' => false, 'message' => $res ));
		}

    }

}

?>