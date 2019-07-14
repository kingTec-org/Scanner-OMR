<?php
	class Exam_model extends CI_Model
	{
		public function check($id=NULL)
		{
			$org = $this->db->get('exam');
			$user = count($org->num_rows());
			if(is_null($id))
			{
				return TRUE;
			}
			elseif($user > 0)
			{
				return TRUE;
			}
			else
			{
				redirect('exam/add/');
			}
		}

		public function get_show_data()
		{
			$this->load->config('skyq/my_config');
			$exam_type = $this->config->item('exam_type');
			$query="ID,exam_type,title,date,subjects,(SELECT title FROM batch WHERE exam.batch=batch.ID) AS batch,exam_status";
			$this->db->select($query);
			$org = $this->db->get('exam');
			if ($org->num_rows() > 0) 
			{
				$data['data'] = $org->result_array();
				foreach ($data['data'] as $key => $value) {
					$data['data'][$key]['exam_type'] = $exam_type[$value['exam_type']];
					$data['data'][$key]['subjects'] = $this->get_all_subjects_title($value['subjects']);
					$data['data'][$key]['link1'] = ' <a class="btn btn-xs gray-bg" href="'.base_url('exam/step2_add/'.$value['ID']).'"> Update Answers </a>';
					$data['data'][$key]['link2'] = '<button class="btn btn-xs btn-info" onClick="window.open(\''.base_url('exam/print_omr_sheet/'.$value['ID']).'\',\'_blank\',\'width=800,height=600\')"> Print OMR Sheet </button>';
					if($value['exam_status'] == 'S')
					{
						$data['data'][$key]['link3'] = ' <button class="btn btn-xs btn-warning" onClick="window.open(\''.base_url('exam/view_result/'.$value['ID']).'\',\'_self\')"> View Result </button>';
						$data['data'][$key]['link4'] = '';
					}
					else
					{
						$data['data'][$key]['link3'] = ' <a class="btn btn-xs btn-success" href="'.base_url('exam/upload_omr_sheet/'.$value['ID']).'"> Upload OMR Sheet </a>';
						$data['data'][$key]['link4'] = ' <button class="btn btn-xs btn-danger" onClick="deletef(\''.$value['ID'].'\',\''.base_url('exam/delete').'\')"> Delete</button>';
					}
				}
			}
			else
			{
				$data = array('data'=>array());
			}
			return $data;
		}

		public function delete($id = NULL)
		{
			$this->load->helper('file');
			$this->db->trans_start();
			$orge = $this->db->get_where('exam',array('ID'=>$id));
			$rec = $orge->result_array();
			$orgs = $this->db->get_where('sections',array('exam_ID'=>$id));
			$rec['sections'] = $orgs->result_array();
			if(!empty($rec['sections']))
			{
				foreach ($rec['sections'] as $key => $value) {
					$orgq = $this->db->get_where('questions',array('section_ID'=>$value['ID']));
					$rec['sections'][$key]['questions'] = $orgq->result_array();
				}
			}
			//print_r($rec);
			//print_r($rec_sections);
			//print_r($rec_questions);
			$this->db->where('ID', $id);
      		$result = $this->db->delete('exam');
      		$this->db->trans_complete();
      		if ($this->db->trans_status() === FALSE)
			{
			    $result = FALSE;
			}
			else
			{
				$deleted_data = array('user'=>$this->session->userdata('ID'),'deleted_on'=>date('Y-m-d h:i:s'),'table'=>'exam','data'=>$rec);
				$data = "\r\n\r\n".json_encode($deleted_data);
				if (!write_file(APPPATH.'/logs/deleted_data/'.date('Y_m_d').'.skyq', $data,"a"))
				{
				    $result = FALSE;
				}
				else
				{
				    $result = TRUE;
				}
			}
      		return $result;
		}

		public function step1_add()
		{
			$post = $this->input->post(NULL);
			$post_data = $this->step1_get_post_data($post);
			if(!empty($post_data['sections']))
			{
				$post_data['sections'] = $this->step1_get_section_data($post_data['sections']);
			}

			return $this->step1_add_data($post_data);
		}

		public function step2_add($id)
		{
			$post = $this->input->post(NULL);
			$new_arr = array();
			foreach ($post as $key => $value) {
				$id = explode('-',$key);
				if($id[1] == 'key_answer')
				{
					$new_arr[$id[0]][$id[1]] = @$new_arr[$id[0]]['num_type'].$value;
					unset($new_arr[$id[0]]['num_type']);
				}
				else{
					$new_arr[$id[0]][$id[1]] = (is_array($value)) ? implode(',',$value) : $value;
				}
			}
			// print_r($new_arr);
			return $this->step2_add_data($new_arr);
		}

		private function step1_get_post_data($post)
		{
			$sections = $sub_sections = array();
			$post['created_by'] = $this->session->userdata('ID');

			foreach ($post as $key => $value) {
				if(strpos($key,'-') !== FALSE && array_key_exists('subjects',$post))
				{
					$sec_data = explode('-',$key);
					$sections[$sec_data[1]][$sec_data[0]] = (is_array($value)) ? implode(',',$value) : $value;
					unset($post[$key]);
				}
				else{
					$post[$key] = (is_array($value)) ? implode(',',$value) : $value;
				}
			}

			if($post['exam_type'] == '1' || $post['exam_type'] == '2' || $post['exam_type'] == '3')
			{
				$subject_arr = explode(',',$post['subjects']);
				foreach ($sections as $key => $value) {
					foreach ($subject_arr as $subject) {
						$value['subject_ID'] = $subject;
						$value['created_by'] = $this->session->userdata('ID');
						$sub_sections[] = $value;
					}
				}
			}
			else{
				foreach ($sections as $key => $value) {
					$value['created_by'] = $this->session->userdata('ID');
					$sub_sections[] = $value;
				}
			}

			return array('post'=>$post,'sections'=>$sub_sections);
		}

		private function step1_get_section_data($sections)
		{
			foreach ($sections as $key1 => $value1) {
				$qns = array_slice($value1,2);
				$subject = @$qns['subject_ID'];
				unset($qns['subject_ID']);
				$diff = array_diff($value1,$qns);
				$diff['subject_ID'] = $subject;
				$sections[$key1] = $diff;
				for($i=1;$i<=$value1['no_of_qn'];$i++)
				{
					$qns['qn_no'] = $i;
					$qns['created_by'] = $this->session->userdata('ID');
					$sections[$key1]['questions'][$i-1] = $qns; 
				}
			}
			return $sections;
		}

		private function step1_add_data($data)
		{
			$this->form_validation->set_data($data['post']);
			if ($this->form_validation->run('exam') !== FALSE)
			{
				$this->db->trans_start();
				$this->db->insert('exam',$data['post']);
				foreach ($data['sections'] as $key => $section) {

					$section['exam_ID'] = $this->find_max_id('exam','ID');
					$qn = $section['questions'];
					unset($section['questions']);
					$this->db->insert('sections',$section);
					$qn = array_map(function($arr){
					    return $arr + ['section_ID' => $this->find_max_id('sections','ID')];
					}, $qn);
					$this->db->insert_batch('questions',$qn);
				}
				$this->db->trans_complete();
				if($this->db->trans_status() === FALSE)
				{
					return FALSE;
				}
				else{
					return TRUE;
				}

			}
			else{
				return validation_errors();
			}
		}

		private function step2_add_data($data)
		{
			$this->db->trans_start();
			$this->db->update_batch('questions', $data, 'ID');
			$this->db->trans_complete();
			if($this->db->trans_status() === FALSE)
			{
				return FALSE;
			}
			else{
				return TRUE;
			}
		}

		private function find_max_id($table,$column)
		{
			$this->db->select_max($column);
			$query = $this->db->get($table);
			if($query->num_rows() > 0)
			{
				$res = $query->row_array();
				return $res[$column];
			}
			else{
				return 1;
			}
		}

		public function get_exam_data($id=NULL,$get_students_by_batch="NO",$need_chapters="NO",$student_ID=NULL)
		{
			if(!is_null($id))
			{
				if($get_students_by_batch == 'YES')
				{
					return array('exam'=>$this->get_exam($id),'students'=>$this->get_students($id));
				}
				else{
					if($need_chapters == 'YES')
					{
						return (is_null($student_ID)) ? array('exam'=>$this->get_exam($id),'subject_chapter'=>$this->get_chapters($id)) : array('exam'=>$this->get_exam($id),'subject_chapter'=>$this->get_chapters($id),'student_result'=>$this->get_record_by_student($student_ID,$id,'YES'));
					}
					else{
						$only_exams = $need_chapters; //Converting argument need chapters as only exams
						return $this->get_exam($id,$only_exams);
					}
				}
			}
			else{
				log_message('error', 'Exam > Exam_model > get_exam_data > Argument id cannot be null.');
				return FALSE;
			}
		}

		private function get_exam($id,$only_exams="NO")
		{
			$select="ID,exam_type,title,date,subjects,(SELECT title FROM batch WHERE exam.batch=batch.ID) AS batch";
			$this->db->select($select);
			$exam = $this->db->get_where('exam',array('ID'=>$id));
			if ($exam->num_rows() == 1) 
			{
				if($only_exams == "ONLY") // Get Only Exam Data
				{
					$row = $exam->row_array();
					$row['subjects'] = $this->get_all_subjects_title($row['subjects']);
					return $row;
				}
				else if($only_exams == "RESULT") // Get Result Data
				{
					$row = $exam->row_array();
					return $this->get_result($row);
				}
				else // Get All Sections and Questions
				{
					return $this->get_sections($exam->row_array()); 
				}
			}
			else{
				log_message('error', 'Exam > Exam_model > get_exam_data:get_exam > Data not found with this argument id "'.$id.'"');
				return FALSE;
			}
		}

		private function get_sections($exam_data)
		{
			$sections_arr = array();
			if($exam_data['exam_type'] == 1 || $exam_data['exam_type'] == 2 || $exam_data['exam_type'] == 3)
			{
				$subject_IDs = explode(',',$exam_data['subjects']);
				foreach ($subject_IDs as $subject_ID) {
					$subject_title = $this->get_subject_title($subject_ID);
					$sections_arr[$subject_title] = $this->get_sections_wrt_subject($exam_data,$subject_ID);
				}
			}
			else{
				$subject_title = $this->get_all_subjects_title($exam_data['subjects']);
				$sections_arr[$subject_title] = $this->get_sections_wrt_subject($exam_data);
			}
			return $this->get_questions($sections_arr,$exam_data);
		}

		private function get_questions($sections_arr,$exam_data)
		{
			$exam_data['z_sections'] = $sections_arr;
			foreach($sections_arr as $sections_key=>$sections_data)
			{
				foreach($sections_data as $key=>$sections)
				{
					$select = "ID,section_ID,chapters,level,qn_no,qn_type,length,IFNULL(key_answer,0) AS key_answer";
					$this->db->select($select);
					$questions = $this->db->get_where('questions',array('section_ID'=>$sections['ID']));
					if ($questions->num_rows() > 0) 
					{
						$exam_data['z_sections'][$sections_key][$key]['z_questions'] = $questions->result_array();
					}
					else{
						log_message('error', 'Exam > Exam_model > get_exam_data:get_questions > Data not found with this argument id "'.$id.'"');
						return FALSE;
					}
				}
			}
			return $exam_data;
		}

		public function get_students($exam_id)
		{
			$select="ID,name,roll_no,(SELECT title FROM batch WHERE student.batch_ID=batch.ID) AS batch";
			$where="batch_ID IN (SELECT batch from exam WHERE ID = ".$exam_id.")";
			$this->db->select($select);
			$this->db->where($where);
			$org = $this->db->get('student');
			return $org->result_array();
		}

		private function get_subject_title($subject_ID)
		{
			$this->db->select('title');
			$query = $this->db->get_where('subject',array('ID'=>$subject_ID));
			if($query->num_rows() > 0)
			{
				$row = $query->row_array();
				return $row['title'];
			}
		}

		public function get_all_subjects_title($subject_IDs)
		{
			$subject_title_arr = array();
			$subject_IDs = explode(',',$subject_IDs);
			foreach ($subject_IDs as $subject_ID) {
				$subject_title_arr[] = $this->get_subject_title($subject_ID);
			}
			return (empty($subject_title_arr)) ? '' : implode(', ',$subject_title_arr);
		}

		private function get_sections_wrt_subject($exam_data,$subject_ID=NULL)
		{
			$select = "ID,exam_ID,section,no_of_qn,subject_ID";
			$this->db->select($select);
			$sections = $this->db->get_where('sections',array('exam_ID'=>$exam_data['ID'],'subject_ID'=>$subject_ID));
			if ($sections->num_rows() > 0) 
			{
				return $sections->result_array();
			}
			else{

				log_message('error', 'Exam > Exam_model > get_exam_data:get_sections > Data not found with this argument id "'.$exam_data['ID'].'"');
				return FALSE;
			}
		}

		public function print_omr_sheet($id=NULL)
 		{
 			$data = $this->get_exam_data($id,'YES');
 			return $data;
 		}

 		private function get_chapters($id)
		{
			$select="subject_ID,(SELECT title FROM subject WHERE sections.subject_ID=subject.ID) AS subject_title";
			$where=array('exam_ID' => $id);
			$this->db->select($select);
			$this->db->where($where);
			$this->db->group_by('subject_ID');
			$org = $this->db->get('sections');
			$subjects = $org->result_array();
			$subject_arr = array();
			foreach ($subjects as $key => $subject) {
				if(is_null($subject['subject_ID']))
				{
					break;
				}
				else{
					$subject_arr[$subject['subject_title']] = $this->get_chapters_by_subject($subject['subject_ID']);
				}
			}
			return $subject_arr;
		}

		private function get_chapters_by_subject($subject_id)
		{
			$this->db->select('ID,title');
			$org = $this->db->get_where('chapter',array('subject_ID'=>$subject_id));
			if($org->num_rows() > 0)
			{
				return $org->result_array();
			}
			else{
				return array();
			}
		}

		private function get_result($exam_data)
		{
			$exam_data['subjects'] = $this->get_all_subjects_title($exam_data['subjects']);
			$exam_data['questions'] = $this->get_total_questions($exam_data['ID']);
			$exam_data['questions']['total'] = count($exam_data['questions']);
			$exam_data['questions']['marks'] = array_sum(array_column($exam_data['questions'], 'max'));
			return $this->get_students_with_results($exam_data);
		}

		public function get_total_questions($exam_ID)
		{
			$this->db->select('ID');
			$org = $this->db->get_where('sections',array('exam_ID'=>$exam_ID));
			if($org->num_rows() > 0)
			{
				$section_IDs = array_column($org->result_array(),'ID');
				$sections = implode('" OR section_ID="',$section_IDs);

				$this->db->select('level,ID,max,min,leave');
				// $this->db->group_by('level');
				$this->db->where('section_ID="'.$sections.'"');
				$query = $this->db->get('questions');
				if($org->num_rows() > 0)
				{
					$result = $query->result_array();
					return $result;
				}
				else{
					return 0;
				}
			}
			else{
				return 0;
			}
		}

		public function get_students_with_results($exam_data)
		{
			$student_present = $avg_attempted = $avg_correct = $avg_incorrect = $avg_easy = $avg_medium = $avg_hard = $avg_marks_obtained = 0;
			$exam_data['student_record'] = $this->get_students($exam_data['ID']);
			foreach ($exam_data['student_record'] as $key => $value) {
				$record_by_student = $this->get_record_by_student($value['ID'],$exam_data['ID']);
				if(!empty($record_by_student))
				{
					++$student_present;
					$qn_attempted = count($record_by_student);
					$marks_obtained = array_sum(array_column($record_by_student,'marks_obtained'));
					$avg_attempted += $qn_attempted;
					$avg_marks_obtained += $marks_obtained;
					
					$qn_correct = $qn_incorrect = $qn_easy = $qn_medium = $qn_hard = 0; 
					foreach ($record_by_student as $skey => $svalue) {
						if($svalue['ans_status'] == 'Correct')
						{
							++$qn_correct;
							++$avg_correct;
						}
						else if($svalue['ans_status'] == 'Incorrect'){
							++$qn_incorrect;
							++$avg_incorrect;
						}

						if(array_key_exists('level', $svalue))
						{
							switch($svalue['level'])
							{
								case 'E' :
									++$qn_easy;
									++$avg_easy;
									break;
								case 'M' :
									++$qn_medium;
									++$avg_medium;
									break;
								case 'H' :
									++$qn_hard;
									++$avg_hard;
									break;
								default:
									break;
							}
						}
					}
				}
				else{
					$qn_attempted = $marks_obtained = $qn_correct = $qn_incorrect = $qn_easy = $qn_medium = $qn_hard = '-AB-';
				}
				//Table Data
				$exam_data['student_record'][$key]['qn_attempted'] = $qn_attempted;
				$exam_data['student_record'][$key]['marks_obtained'] = $marks_obtained;
				$exam_data['student_record'][$key]['qn_correct'] = $qn_correct;
				$exam_data['student_record'][$key]['qn_incorrect'] = $qn_incorrect;
				$exam_data['student_record'][$key]['qn_easy'] = $qn_easy;
				$exam_data['student_record'][$key]['qn_medium'] = $qn_medium;
				$exam_data['student_record'][$key]['qn_hard'] = $qn_hard;			
			}

			//Average Data
			if(!empty($student_present))
			{
				$exam_data['avg_attempted'] = $avg_attempted/$student_present;
				$exam_data['avg_marks_obtained'] = $avg_marks_obtained/$student_present;
				$exam_data['avg_correct'] = $avg_correct/$student_present;
				$exam_data['avg_incorrect'] = $avg_incorrect/$student_present;
				$exam_data['avg_easy'] = $avg_easy/$student_present;
				$exam_data['avg_medium'] = $avg_medium/$student_present;
				$exam_data['avg_hard'] = $avg_hard/$student_present;
			}
			return $exam_data;
		}

		private function get_record_by_student($student_ID,$exam_ID,$need_student_result="NO")
		{
			$select = ($need_student_result == 'NO') ? 'ID,question_ID,answer,(SELECT max FROM questions WHERE student_result.question_ID=questions.ID) AS qn_max,(SELECT min FROM questions WHERE student_result.question_ID=questions.ID) AS qn_min,(SELECT key_answer FROM questions WHERE student_result.question_ID=questions.ID) AS qn_answer,(SELECT level FROM questions WHERE student_result.question_ID=questions.ID) AS qn_level': 'question_ID,answer,(SELECT max FROM questions WHERE student_result.question_ID=questions.ID) AS qn_max,(SELECT min FROM questions WHERE student_result.question_ID=questions.ID) AS qn_min,(SELECT key_answer FROM questions WHERE student_result.question_ID=questions.ID) AS qn_answer';
			$this->db->select($select);
			$org = $this->db->get_where('student_result',array('student_ID'=>$student_ID,'exam_ID'=>$exam_ID));
			if($org->num_rows() > 0)
			{
				$result = $org->result_array();
				$new_result = array();
				$questions = $this->get_total_questions($exam_ID);
				foreach ($result as $key => $value) {
					$new_result[$value['question_ID']] = $value;
				}
				foreach ($questions as $qkey => $qvalue) {
					if(array_key_exists($qvalue['ID'],$new_result))
					{
						$questions[$qkey]['answer'] = $new_result[$qvalue['ID']]['answer'];
						if($new_result[$qvalue['ID']]['qn_answer'] == $new_result[$qvalue['ID']]['answer'])
						{
							$questions[$qkey]['marks_obtained'] = $qvalue['max'];
							$questions[$qkey]['ans_status'] = 'Correct';
						}
						else
						{
							$questions[$qkey]['marks_obtained'] = $qvalue['min'];
							$questions[$qkey]['ans_status'] = 'Incorrect';
						}
					}
					else{
						$questions[$qkey]['marks_obtained'] = $qvalue['leave'];
						$questions[$qkey]['ans_status'] = 'Leave';
						$questions[$qkey]['answer'] = NULL;
						unset($questions[$qkey]['level']);
					}
				}

				if($need_student_result == 'NO')
				{
					return $questions;
				}
				else{

					$data['ans'] = array_column($questions, 'answer', 'ID');
					$data['marks'] = array_column($questions, 'marks_obtained', 'ID');
					$data['obtained'] = array_sum($data['marks']);
					return $data;
				}
			}
			else{
				return array();
			}
		}

		public function sheet_upload_step($step)
		{
			switch ($step) {
				case 1:
					$this->load->library('upload');
					return $this->upload_pdf();
					break;
				
				case 2:
					return $this->convert_pdf_to_jpg();
					break;
				case 3:
					return $this->omr_evaluation_start();
					break;
				case 4:
					break;
				case 5:
					break;
				default:
					break;
			}
		}

		private function upload_pdf()
		{
			$post = $this->input->post();
			$filename = md5(time()).'-'.$_FILES['userfile']['name'];

			$config['file_name']          	= $filename;
			$config['upload_path']          = PDFPATH;
            $config['allowed_types']        = 'pdf';
            $this->upload->initialize($config);
            if (!$this->upload->do_upload('userfile'))
            {
                return array('error' => $this->upload->display_errors());
            }
           	return array('ID'=>$post['id'],'File'=>$filename);
		}

		private function convert_pdf_to_jpg()
		{
			$post = $this->input->post();
			$folder = str_replace('.pdf','',$post['File']);
			$imagick = new Imagick(); 
			if(!file_exists(PDFPATH.$folder))
			{
				mkdir(PDFPATH.$folder, 0777);
			}
			$imagick->setResolution(250,250);
			$imagick->readImage(PDFPATH.$post['File']); 
			$is_converted = $imagick->writeImages(PDFPATH.$folder.'/i.jpg', true); 
			if(!$is_converted)
			{
				return array('error'=>'Something went wrong while converting PDF to JPG');
			}
			return array('data'=>$post);
		}

		private function omr_evaluation_start()
		{
			$post = $this->input->post();
			return array('data'=>$post);
		}
	}
?>