<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Patients extends Secure {

	function __construct() {
        parent::__construct();

		$this->load->model('Patient');

		$this->load->language('patients', 'english');
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
			// application/views/some_folder/header
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('full-column') // application/views/layouts/two_col.php
			->build('manage', $data); // views/welcome_message);
		
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

	function load_ajax() {
	
		if ($this->input->is_ajax_request()) 
		{	
			$this->load->library('datatables');
	        $isfiltered = $this->input->post('filter');

	        $this->datatables->select("users.id as id, CONCAT(IF(up.lastname != '', up.lastname, ''),',',IF(up.firstname != '', up.firstname, '')) as fullname, username, email, r.role_name as rolename, DATE_FORMAT(users.created, '%M %d, %Y') as created, avatar, DATE_FORMAT(CONCAT(IF(up.bYear != '', up.bYear, ''),'-',IF(up.bMonth != '', up.bMonth, ''),'-',IF(up.bDay != '', up.bDay, '')), '%M %d, %Y') as birthday, address, mobile, blood_type, DATE_FORMAT(users.last_login, '%M %d, %Y') as last_login, users.license_key as lic", false);
	        
			$this->datatables->where('users.deleted', 0);
			$this->datatables->where('users.role_id', 82);
			$this->datatables->where('users.license_key', $this->license_id);
			if($isfiltered > 0){
				$this->datatables->where('DATE(created) BETWEEN ' . $this->db->escape($isfiltered) . ' AND ' . $this->db->escape($isfiltered));
			}
			$this->datatables->join('users_profiles as up', 'users.id = up.user_id', 'left', false);
	        $this->datatables->join('users_role as r', 'users.role_id = r.role_id', 'left', false);
	        
	        $this->datatables->from('users');

	        echo $this->datatables->generate('json', 'UTF-8');
    	}else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
   function view($id = -1){
   	
        if ($this->input->is_ajax_request()) 
		{
			$this->load->model('roles/Role');
			$this->load->library('location_lib');

	        $data['info'] = $this->Patient->get_info($id);
			
			$roles = array('' => 'Select');

			foreach ($this->Role->get_all($this->license_id, $this->admin_role_id, 1)->result_array() as $row) {
				$roles[$row['role_id']] = $row['role_name'];
			}
			$data['roles'] = $roles;
			$data['option'] = $this->session->userdata('option');

	        $this->load->view("form", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
	function doSave($id = -1){
		
		$bod = explode('/', $this->input->post('bod'));
		$clearpass = random_string('numeric',8);
		$this->load->library('pass_secured');

		if ($id==-1) {
			$user_data=array(
				'username'      =>str_replace(' ', '', $this->input->post('first_name').'_'.$clearpass),        
				'email'         =>strtolower(str_replace(' ', '', $this->input->post('first_name').'_'.$clearpass.'@sample.com')),
				'password'      =>$this->pass_secured->encrypt(date('Ymd')),
				'role_id'		=>82,
				'license_key'	=>$this->license_id,
				'last_ip'       =>$this->input->ip_address(),
				'created'       => date('Y-m-d H:i:s'),
				'token'			=> date('Ymd').'-'.random_string('numeric',8)
			);
		} else {
			$user_data=array();
		}

		$profile_data = array(
			'firstname'		=>$this->input->post('first_name'),
			'mi'			=>$this->input->post('mi'),
			'lastname'		=>$this->input->post('last_name'),
			'bMonth'		=>$bod[1],
			'bDay'			=>$bod[0],
			'bYear'			=>$bod[2],
			'gender'		=>$this->input->post('gender'),
			'blood_type'	=>$this->input->post('blood_type'),
			'home_phone'	=>$this->input->post('home_phone'),
			'mobile'		=>$this->input->post('mobile'),
			'address'		=>$this->input->post('address'),
			'zip'			=>$this->input->post('zip'),
			'city'			=>$this->input->post('city'),
			'state'			=>$this->input->post('state'),
			'country'		=>$this->input->post('country')
		);

		$extend_data = array(
			'other_info'	=>$this->input->post('other_info'),
			'comments'		=>$this->input->post('comments')
		);
		
		if($this->Patient->save($user_data, $profile_data, $extend_data, $id))
		{
			if($id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$profile_data['lastname']));
			}
			else //previous employee
			{
				echo json_encode(array('success'=>true,'message'=>$profile_data['lastname']));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$profile_data['lastname']));
		}
			
	}
	
    function reset($id = -1){
    	if ($this->input->is_ajax_request()) 
		{
	    	$data['info'] = $this->Patient->get_profile_info($id);
	        $this->load->view("reset", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }

    function delete($user_id){

    	if ($this->users->delete_user($user_id)) {
			echo json_encode(array('success' => true, 'message' => 'User successfully deletd!'));
		} else {
			echo json_encode(array('success' => false, 'message' => 'User cannot be deletd!'));
		}

    }

     function update($id = -1){
    	if ($this->input->is_ajax_request()) 
		{
	    	$data['info'] = $this->Patient->get_profile_info($id);
	        $this->load->view("update", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }

    function details($id = -1){
    	
    	if ($this->input->is_ajax_request()) 
		{
	    	$data['info'] = $this->Patient->get_profile_info($id);
	        $this->load->view("detail", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
	function encode($type, $id){
	
		redirect('patients/records/'.$type.'/'.url_base64_encode($id));
	}
	
	function decoded($type, $id){

		redirect('patients/records/'.$type.'/'.url_base64_encode($id));
		
	}
	
	function records($type = 'summary', $id){
		
		if($id == ''){
			redirect('patients/decoded/summary/'.$id);
		}

		$this->load->model('queings/Queing');
		$this->load->model('records/Record');

		$data['module'] = 'Patient Records';
		
		if ($this->input->is_ajax_request()) 
		{
			$patient_id = url_base64_decode($id);
			
			$data['type'] = $type; 
			$data['info'] = $this->Patient->get_profile_info($patient_id);
			
			$this->load->view('record', $data);
        } 
		else
		{
			$this->_init($data);
		}
	}
	
	function get_record(){
		if ($this->input->is_ajax_request()) 
		{
			$this->load->model('records/Record');

			$type = $this->input->post('type');
			$data['id'] = url_base64_decode($this->input->post('id'));

			$data['type'] = $type;
			$data['latest'] = $this->Record->get_current_data($type, $data['id'], date('Y-m-d'));
			$data['pr_result'] = $this->Record->get_all_data($type, $data['id'], 'no');//segment 3 
			$data['m_result'] = $this->Record->get_all_data($type, $data['id'], 'yes');//segment 3 
			
			$this->load->view('records/records/'.$type.'/manage', $data);
		}
	}
	
	function get_record_files(){
		if ($this->input->is_ajax_request()) 
		{
			$this->load->model('records/Record');

			$type = $this->input->post('type');
			$data['id'] = url_base64_decode($this->input->post('id'));
			$data['type'] = $type;
			$data['latest'] = $this->Record->get_current_data($type, $data['id'], date('Y-m-d'));
			$data['result'] = $this->Record->get_all_file_data($type, $data['id']);//segment 3 
			$result  = array();

			foreach ( $data['result']  as $file ) {

				$obj['name'] = $file['file_name'];
				$obj['size'] = 12345;
				$result[] = $obj;
				
			}
			
			header('Content-type: text/json');              //3
			header('Content-type: application/json');
			echo json_encode($result);
	
		}
	}
}
