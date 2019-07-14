<?php
	class Login extends CI_Controller{
	    public function __construct()
	    {
	        parent::__construct();
	        $this->load->model('login_model');
	        $this->skyq = $this->config->item('skyq');
		}

		public function index($lang="english")
		{
			$this->data['test'] = '';
			$this->load->view('others/login_view',$this->data);
		}

		public function lock()
		{
			if($this->login_model->lock())
			{
				$this->data['name'] = $this->session->userdata('Name');
				$this->data['email'] = $this->session->userdata('Email');
				$this->load->view('others/locked_view',$this->data);
			}
			else
			{	
				// redirect($this->skyq['default_login_page']);
				echo "Not working well";
				/*$this->data['name'] = $this->session->userdata('Name');
				$this->data['email'] = $this->session->userdata('Email');
				$this->load->view('others/locked_view',$this->data);*/
			}	
		}


		public function process($page="Login")
		{
			define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	        if(IS_AJAX)
	        {
	        	if($page === "Login")
	        	{
	        		$this->clearAttachment();
	        		$u = $this->input->post('email');
	            	$p = $this->input->post('password');
	            	echo json_encode($this->login_model->authenticate($u,$p));
	        	}
	        	elseif ($page === "Lock") 
	        	{
	        		$p = $this->input->post('password');
	            	echo json_encode($this->login_model->authenticate(NULL,$p));
	        	}
	        	else
	        	{
	        		redirect($this->skyq['default_login_page']);
	        		return FALSE;
	        	}
            }
	        else
	        {
	        	redirect($this->skyq['default_login_page']);
	        	return FALSE;
	        }
		}

		public function logout()
		{
			if($this->login_model->logout())
			{
				redirect('Login');
			}
			else {
				redirect($this->skyq['default_login_page']);
			}
		}

		public function clearAttachment($value='')
		{
			$files = glob(getcwd().'/attachments/*');
			foreach($files as $file){ 
			  if(is_file($file))
			    unlink($file);
			}
		}

	}

?>