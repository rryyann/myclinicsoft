<?php
require_once APPPATH. 'modules/secure/controllers/Secure.php';

class Appointments extends Secure {

	function __construct() {
        parent::__construct();
        $this->load->model('Appointment');
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
			->set_partial('sidebar', 'include/sidebar') //third param optional $data
			->set_partial('ribbon', 'include/ribbon', $data) //third param optional $data
			->set_partial('footer', 'include/footer') //third param optional $data
			->set_partial('shortcut', 'include/shortcut') //third param optional $data
			->set_metadata('author', 'Randy Rebucas')
			//->inject_partial('header', '<h1>Hello World!</h1>')  //third param optional $data
			->set_layout('full-column') // application/views/layouts/two_col.php
			->build('manage', $data); // views/welcome_message);
		
	}

	function index()
	{
		$data['module'] = 'Appointments';
		if ($this->input->is_ajax_request()) 
		{
			
			$this->load->view('manage', $data);
        } 
		else
		{
			$this->_init($data);
			
		}
	}
	
	function get(){
		echo json_encode($this->Appointment->get_all($this->license_id)->result_array());
	}
	
	function view($id = -1){

		$this->load->model('common/Common');
		$this->load->model('patients/Patient');

        if ($this->input->is_ajax_request()) 
		{

			$data['info'] = $this->Appointment->get_info($id);
			
			$doctors = array('' => 'Select');

			foreach ($this->Common->get_all_clients()->result_array() as $row) {
				$doctors[$row['license_key']] = $row['firstname'].', '.$row['lastname'];
			}
			$data['doctors'] = $doctors;
			
			$patients = array('' => 'Select');

			foreach ($this->Patient->get_all()->result_array() as $row) {
				$patients[$row['id']] = $row['firstname'].', '.$row['lastname'];
			}
			$data['patients'] = $patients;
			
	        $this->load->view("form", $data);
			
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
	function doSave($id = -1){
		
		$appointment_data = array(
			'schedule_date'		=>$this->input->post('schedule_date'),
			'schedule_time'		=>$this->input->post('schedule_time'),
			'description'		=>$this->input->post('description'),
			'title'				=>$this->input->post('title'),
			'user_id'			=>$this->input->post('user_id') ? $this->input->post('user_id') : $this->user_id,
			'license_key'		=>$this->input->post('license_key'),
			'status'			=>$this->input->post('status'),
			'doctor_note'		=>$this->input->post('doctor_note'),
			'patient_note'		=>$this->input->post('patient_note')
		);
		
		if($this->Appointment->save($appointment_data, $this->license_id, $id))
		{
			if($id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$appointment_data['title']));
			}
			else 
			{
				echo json_encode(array('success'=>true,'message'=>$appointment_data['title']));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$appointment_data['title']));
		}
			
	}
	
	function details($id = -1){

    	if ($this->input->is_ajax_request()) 
		{
			$this->load->model('common/Common');
			$this->load->model('patients/Patient');

	    	$data['info'] = $this->Appointment->get_info($id);
			$data['client_id'] = $this->Common->get_subscription_info($data['info']->license_key);
			$data['client_info'] = $this->Patient->get_info($data['client_id']->user_id);
			
	        $this->load->view("detail", $data);
	    }else{
	    	$this->session->set_flashdata('alert_error', 'Sorry! Page cannot open by new tab');
            redirect('');
	    }
    }
	
    function delete($id){

    	if ($res = $this->Appointment->delete($id)) {
			echo json_encode(array('success' => true, 'message' => 'Appointment successfully deletd!'));
		} else {
			echo json_encode(array('success' => false, 'message' => $res ));
		}

    }

}
