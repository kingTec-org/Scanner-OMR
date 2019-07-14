<?php
	class Chapter extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();
		if($this->login_model->check_login())
		{
			$this->data['Login']['Login_as'] = $this->session->userdata('Login_as');
			$this->data['Login']['Name'] = $this->session->userdata('Name');
			$this->data['Login']['Email'] = $this->session->userdata('Email');
			$this->data['Login']['Id'] = $this->session->userdata('Id');
			$this->load->model('chapter_model');
		}
		else 
		{
			redirect($this->config->item('skyq')['default_login_page']);
		}
 	}

	public function index()
	{
		$this->data['breadcrumb']['heading'] = 'Chapters';
		$this->data['menu_active'] = 'Chapters';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Chapters','path'=>'Chapter'),'Show');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/chapter_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	public function get_show_data()
	{
		$res = $this->chapter_model->get_show_data();
		echo json_encode($res);
 	}

 	public function add($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Add Chapter';
		$this->data['menu_active'] = 'Chapters';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Chapters','path'=>'Chapter'),'Add');
		$check = $this->chapter_model->check($id,$this->data['Login']['Login_as']);
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			$this->chapter_model->add_or_edit();
		}
		else
		{
			if($check)
			{
				if(!is_null($id))
				{
					$this->data['breadcrumb']['heading'] = 'Edit Chapter';  
					$this->data['menu_active'] = 'Chapter';
					$this->data['What'] = 'Edit';
					$this->db->where(array('ID'=>$id));
					$org = $this->db->get('chapter');
					$this->data['View'] = $org->result_array();
				}
				$this->db->select('ID,title');
				$org = $this->db->get('subject');
				$this->data['subject'] = $org->result_array();
				$this->load->view('includes/header',$this->data);
				$this->load->view('pages/chapter_add_edit_view',$this->data);
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
 		$delete_data = $this->chapter_model->delete($item_id);
 		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
		if($delete_data)
 		{
		    if(IS_AJAX)
			{
				echo json_encode($delete_data);	
			}
			else
			{
	 			redirect('chapter');
	 		}
		}
 	}

}
?>