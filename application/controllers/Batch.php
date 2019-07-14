<?php
	class Batch extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();
		if($this->login_model->check_login())
		{
			$this->data['Login']['Login_as'] = $this->session->userdata('Login_as');
			$this->data['Login']['Name'] = $this->session->userdata('Name');
			$this->data['Login']['Email'] = $this->session->userdata('Email');
			$this->data['Login']['ID'] = $this->session->userdata('ID');
			$this->load->model('batch_model');
		}
		else 
		{
			redirect($this->config->item('skyq')['default_login_page']);
		}
 	}

	public function index()
	{
		$this->data['breadcrumb']['heading'] = 'Batches';
		$this->data['menu_active'] = 'Batches';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Batches','path'=>'Batch'),'Show');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/batch_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	public function get_show_data($ajax=NULL)
	{
		$res = $this->batch_model->get_show_data($ajax);
		echo json_encode($res);
 	}

 	public function add($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Add Batch';
		$this->data['menu_active'] = 'Batches';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Batches','path'=>'Batch'),'Add');
		$check = $this->batch_model->check($id,$this->data['Login']['Login_as']);
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			$this->batch_model->add_or_edit();
		}
		else
		{
			if($check)
			{
				if(!is_null($id))
				{
					$this->data['breadcrumb']['heading'] = 'Edit batch';  
					$this->data['menu_active'] = 'Batches';
					$this->data['What'] = 'Edit';
					$this->db->where(array('ID'=>$id));
					$org = $this->db->get('batch');
					$this->data['View'] = $org->result_array();
				}
				$this->load->view('includes/header',$this->data);
				$this->load->view('pages/batch_add_edit_view',$this->data);
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
 		$delete_data = $this->batch_model->delete($item_id);
 		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		if($delete_data)
 		{
		    if(IS_AJAX)
			{
				echo json_encode($delete_data);	
			}
			else
			{
	 			redirect('batch');
	 		}
		}
 	}
}
?>