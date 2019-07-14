<?php
	class Exam extends CI_Controller {
		
	public function __construct()
	{
		parent::__construct();
		if($this->login_model->check_login())
		{
			$this->data['Login']['Login_as'] = $this->session->userdata('Login_as');
			$this->data['Login']['Name'] = $this->session->userdata('Name');
			$this->data['Login']['Email'] = $this->session->userdata('Email');
			$this->data['Login']['Id'] = $this->session->userdata('Id');
			$this->load->model('exam_model');
		}
		else 
		{
			redirect($this->config->item('skyq')['default_login_page']);
		}
 	}

	public function index()
	{
		redirect('exam/show');
	}

	public function show()
	{
		$this->data['breadcrumb']['heading'] = 'Examinations';
		$this->data['menu_active'] = 'Examination';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Examinations','path'=>'exam'),'View All');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/exam_view',$this->data);
		$this->load->view('includes/footer',$this->data);
	}

	public function get_show_data()
	{
		$res = $this->exam_model->get_show_data();
		echo json_encode($res);
 	}

 	public function step1_add($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Create Exam';
		$this->data['menu_active'] = 'Examinations';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Examinations','path'=>'exam'),'Step 1');
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			echo json_encode($this->exam_model->step1_add());
		}
		else
		{
			$this->load->view('includes/header',$this->data);
			$this->load->view('pages/exam_add_edit_view',$this->data);
			$this->load->view('includes/footer',$this->data);			
		}
	}

	public function step2_add($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Update Answers';
		$this->data['menu_active'] = 'Examinations';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Examinations','path'=>'exam'),'Step 2');
		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
        if(IS_AJAX)
		{
			echo json_encode($this->exam_model->step2_add($id));
		}
		else
		{
			$this->load->view('includes/header',$this->data);
			$this->load->view('pages/exam_answers_add_edit_view',$this->data);
			$this->load->view('includes/footer',$this->data);			
		}
 	}

 	public function get_exam_data($id=NULL,$need_chapters='NO')
 	{	
 		echo json_encode($this->exam_model->get_exam_data($id,'NO',$need_chapters));
 	}

 	public function print_omr_sheet($id=NULL)
 	{
 		$this->data['data'] = $this->exam_model->print_omr_sheet($id);
 		$this->load->view('pages/exam_sheet_print_view',$this->data);
 		// echo "<pre>";
 		// print_r($this->data['data']);
 		// echo "</pre>";
 	}

  	public function upload_omr_sheet($id=NULL)
 	{
 		$this->data['breadcrumb']['heading'] = 'Upload OMR Sheet';
		$this->data['menu_active'] = 'Examinations';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Examinations','path'=>'exam'),'Upload Answer Sheet');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/exam_answers_upload_view',$this->data);
		$this->load->view('includes/footer',$this->data);			
 	}

 	public function print_omr_sheet1($id=NULL)
 	{
 		ini_set('memory_limit', '-1');
 		$this->data['data'] = $this->exam_model->print_omr_sheet($id);
		// $this->load->view('pages/exam_sheet_pdf_view',$this->data);
		$html = $this->load->view('pages/exam_sheet_pdf_view',$this->data,true);
		$this->load->library('pdf');
		$pdf = $this->pdf->load('','A4',0,0,5,5,15,5);
		$stylesheet = file_get_contents(base_url('css/pdf.css'));
		$pdf->SetHTMLHeader('<h3 style="text-align: center">OMR ANSWER SHEET</h3>');
		$pdf->WriteHTML($stylesheet,1);
		$pdf->WriteHTML($html,2);
 		// $pdfFilePath = FCPATH."/pdfs/".$id.".pdf";
		// $pdf->Output($pdfFilePath, 'F');
		$pdf->Output();
 	}

 	public function sheet_upload_step($step=1)
 	{
 		echo json_encode($this->exam_model->sheet_upload_step($step));
 	}

 	public function view_result($id)
 	{
 		$this->data['breadcrumb']['heading'] = 'Result';
		$this->data['menu_active'] = 'Examinations';
		$this->data['breadcrumb']['route'] = array(array('title'=>'Examinations','path'=>'exam'),'View');
		$this->load->view('includes/header',$this->data);
		$this->load->view('pages/exam_result_view',$this->data);
		$this->load->view('includes/footer',$this->data);			
 	}

 	public function delete($item_id = NULL)
 	{
 		define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	    if(IS_AJAX)
		{
	 		$delete_data = $this->exam_model->delete($item_id);
			echo json_encode($delete_data);	
		}
		else
		{
 			redirect('exam');
 		}
 	}
}
?>