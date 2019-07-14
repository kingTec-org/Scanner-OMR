<?php
	class Student_model extends CI_Model
	{
		public function check($id=NULL)
		{
			$org = $this->db->get('student');
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
				redirect('student/add/');
			}
		}

		public function add_or_edit()
		{
        	$this->load->helper('security');
			$post = $this->input->post(NULL);
			if($this->form_validation->run('student') !== FALSE)
			{
				if ($this->unique_roll_no($post['roll_no']) !== FALSE)
				{
					if(empty($post['ID']))
					{
						unset($post['ID']);
						$post['created_by'] = $this->session->userdata('ID');
						$this->db->trans_start();
						$result = $this->db->insert('student',$post);
						$this->db->trans_complete();
						if ($this->db->trans_status() === FALSE)
						{
						    $result = FALSE;
						}
						else
						{
							$result = TRUE;
						}
					}
					else
					{
						$this->db->trans_start();
						$post['updated_by'] = $this->session->userdata('ID');
						$this->db->where(array('ID'=>$post['ID']));
				 		$result = $this->db->update('student',$post);
						$this->db->trans_complete();
						if ($this->db->trans_status() === FALSE)
						{
						    $result = FALSE;
						}
						else
						{
							$result = TRUE;
						}
					}				
					if($result === TRUE)
					{
						echo 1;
					}
					else
					{
						echo json_encode($result);
					}
				}
				else{
					echo json_encode('The Roll Number is already assigned to another student of this batch');					
				}
			}
			else{
				echo json_encode(validation_errors());
			}
		}

		public function get_show_data($select=NULL,$id=NULL)
		{
			$select=(is_null($select) && is_null($id)) ? "ID,name,roll_no,description,(SELECT title FROM batch WHERE student.batch_ID=batch.ID) AS batch" : $select;
			$this->db->select($select);
			if(!is_null($id))
			{
				$this->db->where('ID',$id);
			}
			$org = $this->db->get('student');
			if ($org->num_rows() > 0) 
			{
					// var_dump($id);
				if(is_null($id))
				{
					$data['data'] = $org->result_array();
					foreach ($data['data'] as $key => $value) {
						$data['data'][$key]['Actions'] = '<a class="btn btn-sm btn-primary" href="'.base_url('student/add/'.$value['ID']).'"><i class="zmdi zmdi-edit"></i> Edit</a> <button class="btn btn-sm btn-danger" onClick="deletef(\''.$value['ID'].'\',\''.base_url('student/delete').'\')"><i class="zmdi zmdi-delete"></i> Delete</button>';
						unset($data['data'][$key]['batch_ID']);
					}
				}
				else{

					return $org->row_array();
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
			$org = $this->db->get_where('student',array('ID'=>$id));
			$rec = $org->result_array();
			$this->db->where('ID', $id);
      		$result = $this->db->delete('student');
      		$this->db->trans_complete();
      		if ($this->db->trans_status() === FALSE)
			{
			    $result = FALSE;
			}
			else
			{
				$deleted_data = array('user'=>$this->session->userdata('ID'),'deleted_on'=>date('Y-m-d h:i:s'),'table'=>'student','data'=>$rec);
				$data = "\r\n".json_encode($deleted_data);
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

		public function unique_roll_no($roll_no)
		{
			$ID = $this->input->post('ID');
			if(!empty($ID))
			{
				$data['ID != '] = $ID;
			}
			$data['roll_no'] = $roll_no;
			$data['batch_ID'] = $this->input->post('batch_ID');
			$this->db->select('ID');
			$query = $this->db->get_where('student',$data);
			if($query->num_rows() > 0)
			{
                return FALSE;
			}
			else{
				return TRUE;
			}
		}

		public function scorecard($student_ID=NULL,$exam_ID=NULL)
		{
			$this->load->model('exam_model');
			$exam_data = $this->exam_model->get_exam_data($exam_ID,"NO","YES",$student_ID);
			$exam_data['student'] = $this->get_show_data('name,roll_no',$student_ID);
			$exam_data['exam']['subjects'] = $this->exam_model->get_all_subjects_title($exam_data['exam']['subjects']);
			$exam_data['exam']['questions'] = $this->exam_model->get_total_questions($exam_ID);
			$exam_data['exam']['questions']['total'] = count($exam_data['exam']['questions']);
			$exam_data['exam']['questions']['marks'] = array_sum(array_column($exam_data['exam']['questions'], 'max'));
			return $exam_data;
		}

		public function update_answer()
 		{
 			$post = $this->input->post();
 			if(array_key_exists('sign', $post) && array_key_exists('number', $post))
 			{
 				$answer = $post['sign'].$post['number'];
 				unset($post['sign']);
 				unset($post['number']);
 				unset($post['answer']);
 			}
 			else{
 				$answer = $post['answer'];
 				unset($post['answer']);
 			}
 			$this->db->trans_start();
			$org = $this->db->get_where('student_result',$post);
			if($org->num_rows() > 0)
			{
				$this->db->where($post);
		 		$result = $this->db->update('student_result',array('answer'=>$answer));
			}
			else
			{
				$post['created_by'] = $this->session->userdata('ID');
				$post['answer'] = $answer;
		 		$result = $this->db->insert('student_result',$post);
			}
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE)
			{
			    return FALSE;
			}
			else
			{
				return TRUE;
			}
 		}
	}
?>