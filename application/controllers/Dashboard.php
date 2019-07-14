<?php
class Dashboard extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		if($this->login_model->check_login())
		{
			$this->data['Login']['Login_as'] = $this->session->userdata('Login_as');
			$this->data['Login']['Name'] = $this->session->userdata('Name');
			$this->data['Login']['ID'] = $this->session->userdata('Id');
			$this->data['my_config'] = $this->my_config = $this->config->item('skyq');
			$this->load->model('dashboard_model');
            // $this->data['menu'] = $this->setting_model->get_menus($this->data,$this->my_config);
		}
		else 
		{
			redirect($this->config->item('skyq')['default_login_page']);
		}
 	}
	
	function index()
	{
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/home_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	function masters()
	{
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/master_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	public function get()
	{
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	    if(IS_AJAX)
		{  
			echo json_encode($this->dashboard_model->get_details());
		}
	}
	
	public function show_invoice_data($customer_ID = NULL)
	{
		// $date = $this->input->post('Date');
		if(!is_null($customer_ID))
		{
			$data = $this->dashboard_model->show_invoice($customer_ID);
			echo json_encode($data);
		}
		else
		{
			echo 0;
		}
 	}

 	public function show_invoice_data2($customer_ID = NULL,$month_year = NULL)
	{
		// $date = $this->input->post('Date');
		if(!is_null($customer_ID))
		{
			$data = $this->dashboard_model->show_invoice_data($customer_ID,$month_year);
			echo json_encode($data);
		}
		else
		{
			echo 0;
		}
 	}

 	public function show_product($product_ID = NULL)
	{
		// $date = $this->input->post('Date');
		if(!is_null($product_ID))
		{
			$data = $this->dashboard_model->show_stock_data($product_ID);
			echo json_encode($data);
		}
		else
		{
			echo 0;
		}
 	}

	public function show()
	{
		$input = array('US'=>array('ID' => 'USSK10000001 || USSK10000004'));
		$this->load->model('fetch_model');
		$show = $this->fetch_model->show($input,array('Name','Email','>>Age:>fn>library>user_library:calAge(^Password^)'));
		echo "<pre>";
		print_r($show);
		echo "</pre>";

	}
	public function add()
	{
		$this->load->model('form_model');
		$input = array(
			'columns' => array(
					'US>Name'=> 'hf',
					'US>Email'=> 'xyz@gmail.com',
					'P>Client_ID' => 'CMASK10000006',
					'LN>Title' => 'dssdwe',
					'FU>Title' => 'dfsd',
					'P>Project_name' => array('rohan','sagar','sumit'),
				)
			);
		$rules = array(
			'US>Name'=> 'min_length[2]|max_length[5]|alpha',
			'US>Email'=> 'valid_email',
			'P>Client_ID'=> 'is_skyqid|is_skyqid_there[projects.Client_ID]',
			'LN>Title' => 'is_unique[languages.Title]|required',
		);
		$query = $this->form_model->add($input,$rules);
		print_r($query);
	}

	public function edit()
	{
		$this->load->model('form_model');
		$input = array(
			'columns' => array(
					'US>Name'=> 'Saurabh',
					'US>Email'=> 'Saurabh@gmail.com',
					'LN>Title' => 'tamil',
					//'FU>Title' => 'dfsd',
					//'P>Project_name' => array('rohan','sagar','sumit'),
				),
			'where' => array(
					'US>ID' => 'USSK10000001',
					'P>ID' => 'PSK10000003',
					'LN>ID' => 'LNSK10000003',
					//'FU>ID' => 'FUSK10000001'
				)
			);
		$rules = array(
			'Name'=> 'min_length[2]|max_length[10]|alpha',
			'Email'=> 'valid_email'
		);
		$query = $this->form_model->edit($input,$rules);
		print_r($query);
	}

	public function delete()
	{
		$this->load->model('form_model');
		$input = array(
			'US' => array(
				'ID' => 'USSK10000004',
				'Name' => 'Rohan'
				),
			'LN' => array(
				'ID' => 'LNSK10000003'
				)
			);
		$query = $this->form_model->delete($input);
		print_r($query);
	}

	public function chkid()
	{
		$this->load->library('Process/Security_lib');

		$id = 'USSK101';
		if(preg_match("/^[A-Z]{1,2}SK[0-9]/", $id))
		{
			$cache = explode('SK',$id);
			if($cache[1] >= 101)
				echo $id;
		}
		else
		{
			echo preg_last_error();
		}
	}

	public function image()
	{
		$abc = $this->lang_library->get_lang_image('Desert.jpg');
		print_r($abc);
	}

	public function showjson()
	{
		$this->load->model('form_model');
		echo '<pre style="word-wrap: break-word; white-space: pre-wrap;">';
		echo json_encode($this->form_model->show('US'));
		echo '</pre>';
	}

	public function g()
	{
		$this->load->library('process/Security_lib');
		echo $this->security_lib->encrypt_string('1e6328a48d5a4154287ec4a8e2d24df61560b4f7','123456');
	}
	public function primary_check()
	{
		$files = $this->config->item('skyq')['log_files'];
		$extension = $this->config->item('skyq')['log_file_extension'];
		$folder_path = $_SERVER['DOCUMENT_ROOT'].'/SkyqCRM/application/logs/';
		//Check if 'logs' directory exists with permissions '777'
		if(file_exists($folder_path) && octal_permissions(fileperms($folder_path)) === '777')
		{
			foreach($files as $log_file)
			{
				if(file_exists($folder_path.$log_file.$extension))
				{
					echo '<br>'.$folder_path.$log_file.$extension.'<br>';
				}
				else
				{
					$data = $log_file.' file initialised at '.date('Y-m-d H:i:s');
					echo (!write_file(APPPATH.'logs/'.$log_file.$extension, $data,"a")) ? FALSE : $folder_path.$log_file.$extension.'<br>';
				}
			}
			$get_filenames = get_filenames($folder_path);
			foreach ($get_filenames as $key => $filename)
			{
				if(strrpos($filename, '.zip') !== FALSE)
				{
					$filen = preg_replace('"\.zip$"', '.zip.skyq', $filename);
					echo $filen.'<br>';
					rename($folder_path.$filename,$folder_path.$filen);
				}
			}
		}
		else
		{
			return FALSE; 
		}
	}

	public function upload_log()
	{
		$this->load->library('ftp');
		$this->config->load('skyq/my_config');

		$files = $this->config->item('skyq')['log_files'];
		$log_dir = $this->config->item('skyq')['app_log_directory'];
		$extension = $this->config->item('skyq')['log_file_extension'];
		$folder_path =	APPPATH.$log_dir.DIRECTORY_SEPARATOR;

		$ftp_details = $this->config->item('skyq')['ftp_details'];
		if($this->ftp->connect($ftp_details))
		{
			if(file_exists($folder_path) && octal_permissions(fileperms($folder_path)) === '777')
			{
				$c = 0;
				foreach($files as $log_file)
				{
					if(file_exists($folder_path.$log_file.$extension))
					{
						if($this->ftp->upload(APPPATH.$log_dir.DIRECTORY_SEPARATOR.$log_file.$extension,"/".$log_file.$extension,'auto', 0644))
						{
							$c++; 
						}
						else
						{
							$c = 0;
						}
					}
				}
				if($c > 0 )
				{
					echo $this->clear_log();
				}
				else
				{
					return FALSE;
				}
			}
			$this->ftp->close();
		}
		else
		{
			echo "error";
		}
	}

	public function clear_log()
	{
		$files = $this->config->item('skyq')['log_files'];
		$log_dir = $this->config->item('skyq')['app_log_directory'];
		$extension = $this->config->item('skyq')['log_file_extension'];
		$folder_path =	APPPATH.$log_dir.DIRECTORY_SEPARATOR;

		//Check if 'logs' directory exists with '777' permission.
		if(file_exists($folder_path) && octal_permissions(fileperms($folder_path)) === '777')
		{
			foreach($files as $log_file)
			{
				//Check if log files exist
				if(file_exists($folder_path.$log_file.$extension))
				{
					$data = $log_file.' file cleared at '.date('Y-m-d H:i:s');
					if(!write_file(APPPATH.$log_dir.DIRECTORY_SEPARATOR.$log_file.$extension, $data,"w"))
					{
						die('Unable to create '.$log_file.$extension.' file');
					}
				}
			}
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function ftp_func()
	{
		$this->load->library('ftp');
		$this->load->helper('file');
		$config['hostname'] = 'ftp.skyq.in';
		$config['username'] = 'skyqintest@skyq.in';
		$config['password'] = 'saurabh@2992';
		$config['port']     = 21;
		$config['passive']  = FALSE;
		$config['debug']    = TRUE;

		if($this->ftp->connect($config))
		{
			echo "<pre>";
			print_r(get_filenames($_SERVER['DOCUMENT_ROOT'].'/SkyqCRM/application/logs'));
			echo "</pre>";
			$list = $this->ftp->list_files('/');
			//print_r($list);
			print_r(octal_permissions(fileperms($_SERVER['DOCUMENT_ROOT'].'/SkyqCRM/application/logs')));			
			//print_r($this->ftp->mirror($_SERVER['DOCUMENT_ROOT'].'/SkyqCRM/application/files/', '/'));
			/*if($this->ftp->upload($_SERVER['DOCUMENT_ROOT'].'/SkyqCRM/application/files/errorlog.txt', '/myfile.txt', 'ascii', 0600))
			{
				echo 'dasd';
			}
			else
			{
				echo 'failed';
			}*/
		}

	}

	function notify($start=null,$end=null,$print=null)
	{
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(!IS_AJAX)
		{
			$this->data['breadcrumb']['heading'] = 'Notify';  
			$this->data['breadcrumb']['route'] = array(array('title'=>'Dashboard','path'=>'Dashboard'),'Show');
			if ($print==null)
			{
				$this->data['print'] = FALSE; 
				// print_r($this->data['amcDetails']);
				$this->load->view('includes/header',$this->data);
				$this->load->view('pages/notify_view',$this->data);
				$this->load->view('includes/footer',$this->data);
			}
			else
			{
				$this->data['print'] = TRUE;  
				$this->load->view('pages/notify_view',$this->data);
			}
		}
		else
		{
			$this->errorlog_library->entry('Dashboard > notify > ajax function is not called.');
			return FALSE;
		}
	}

	public function contacted()
	{
		header('Content-Type:application/x-json; charset=utf-8');
 		echo json_encode($this->dashboard_model->contacted());
	}

	public function getAlertData($value='')
	{
		header('Content-Type:application/x-json; charset=utf-8');
		// print_r($_POST);
		$date=explode("-", $_POST['date']);
		$start=$this->date_library->date2db(trim($date[0]),'m/d/Y');
		$end=$this->date_library->date2db(trim($date[1]),'m/d/Y');
		// var_dump($start);
		// var_dump($end);
 		echo json_encode($this->dashboard_model->getAlerts($start,$end));
	}

	public function getTimeline()
	{
		header('Content-Type:application/x-json; charset=utf-8');
		// var_dump($_POST['date']);
		if ($_POST['date']==='' || $_POST['date']===null || empty($_POST['date'])) {
			$start=date("Y-m-d");
			$end=date("Y-m-d");
		}
		else
		{
			$date=explode("-", $_POST['date']);
			$start=$this->date_library->date2db(trim($date[0]),'m/d/Y');
			$end=$this->date_library->date2db(trim($date[1]),'m/d/Y');
		}
		// var_dump($start);
		// var_dump($end);
 		echo json_encode($this->dashboard_model->getTimeline($start,$end));
	}

	public function sendManualEmail()
	{
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
 		if (!empty($_POST['email'])) {
 			$this->form_validation->set_rules('email', 'email', 'required|valid_email');
 			if ($this->form_validation->run() == FALSE)
            {
                    echo json_encode($this->form_validation->error_array());
            }
            else
            {
            		$this->load->model('customer_model');
                    echo json_encode($this->customer_model->introMail($_POST['email']));
            }
 		}
 		else
 		{
 			echo json_encode(array('error'=>'Please Check Customer Email'));
 		}
	}

	public function getTodaysTask($branch_ID = NULL,$employee_ID = NULL)
	{
		header('Content-Type:application/x-json; charset=utf-8');
 		echo json_encode($this->dashboard_model->getTodaysTask($branch_ID,null,null,$employee_ID));
	}

	public function type_wise()
	{
		header('Content-Type:application/x-json; charset=utf-8');
 		echo json_encode($this->dashboard_model->type_wise());
	}

	public function date_wise()
	{
		header('Content-Type:application/x-json; charset=utf-8');
 		echo json_encode($this->dashboard_model->date_wise());
	}

	public function waiting_approval()
	{
		header('Content-Type:application/x-json; charset=utf-8');
 		echo json_encode($this->dashboard_model->waiting_approval());
	}

}	
?>