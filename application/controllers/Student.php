<?php
	class Student extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();
		if($this->login_model->check_login())
		{
			$this->data['Login']['Login_as'] = $this->session->userdata('Login_as');
			$this->data['Login']['Name'] = $this->session->userdata('Name');
			$this->data['Login']['Email'] = $this->session->userdata('Email');
			$this->data['Login']['Id'] = $this->session->userdata('Id');
			$this->load->model('student_model');
		}
		else 
		{
			redirect($this->config->item('skyq')['default_login_page']);
		}
 	}

	public function index()
	{
		$this->data['breadcrumb']['heading'] = 'Students';
		$this->data['menu_active'] = 'Students';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Students','path'=>'Student'),'Show');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/student_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	public function get_show_data()
	{
		$res = $this->student_model->get_show_data();
		echo json_encode($res);
 	}

 	public function add($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Add Student';
		$this->data['menu_active'] = 'Students';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Students','path'=>'Student'),'Add');
		$check = $this->student_model->check($id,$this->data['Login']['Login_as']);
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			$this->student_model->add_or_edit();
		}
		else
		{
			if($check)
			{
				if(!is_null($id))
				{
					$this->data['breadcrumb']['heading'] = 'Edit Student';  
					$this->data['menu_active'] = 'Student';
					$this->data['What'] = 'Edit';
					$this->db->where(array('ID'=>$id));
					$org = $this->db->get('student');
					$this->data['View'] = $org->result_array();
				}
				$this->db->select('ID,title');
				$org = $this->db->get('batch');
				$this->data['batch'] = $org->result_array();
				$this->load->view('includes/header',$this->data);
				$this->load->view('pages/student_add_edit_view',$this->data);
				$this->load->view('includes/footer',$this->data);			
			}
			else
			{
	 			return FALSE;
			}
		}
	}

 	public function delete($item_id = NULL)
 	{
 		$delete_data = $this->student_model->delete($item_id);
 		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		if($delete_data)
 		{
		    if(IS_AJAX)
			{
				echo json_encode($delete_data);	
			}
			else
			{
	 			redirect('student');
	 		}
		}
 	}

 	public function scorecard($student_ID = NULL,$exam_ID = NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'View Student Result';
		$this->data['menu_active'] = 'Students';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Students','path'=>'Student'),'Scorecard');
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			echo json_encode($this->student_model->scorecard($student_ID,$exam_ID));
		}
		else
		{
			$this->load->view('includes/header',$this->data);
			$this->load->view('pages/student_scorecard_view',$this->data);
			$this->load->view('includes/footer',$this->data);
		}
 	}

 	public function update_answer()
 	{
 		echo json_encode($this->student_model->update_answer());
 	}
}
?>