<?php
	class Chapter_model extends CI_Model
	{
		public function check($id=NULL)
		{
			$org = $this->db->get('chapter');
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
				redirect('chapter/add/');
			}
		}

		public function add_or_edit()
		{
        	$this->load->helper('security');
			$post = $this->input->post(NULL);
			if($this->form_validation->run('chapter') !== FALSE)
			{
				if(empty($post['ID']))
				{
					unset($post['ID']);
					$post['created_by'] = $this->session->userdata('ID');
					$this->db->trans_start();
					$result = $this->db->insert('chapter',$post);
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
			 		$result = $this->db->update('chapter',$post);
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
				echo json_encode(validation_errors());
			}
		}

		public function get_show_data()
		{
			$query="ID,title,description,(SELECT title FROM subject WHERE chapter.subject_ID=subject.ID) AS subject";
			$this->db->select($query);
			$org = $this->db->get('chapter');
			if ($org->num_rows() > 0) 
			{
				$data['data'] = $org->result_array();
				foreach ($data['data'] as $key => $value) {
					$data['data'][$key]['Actions'] = '<a class="btn btn-sm btn-primary" href="'.base_url('chapter/add/'.$value['ID']).'"><i class="zmdi zmdi-edit"></i> Edit</a> <button class="btn btn-sm btn-danger" onClick="deletef(\''.$value['ID'].'\',\''.base_url('chapter/delete').'\')"><i class="zmdi zmdi-delete"></i> Delete</button>';
					unset($data['data'][$key]['subject_ID']);
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
			$org = $this->db->get_where('chapter',array('ID'=>$id));
			$rec = $org->result_array();
			$this->db->where('ID', $id);
      		$result = $this->db->delete('chapter');
      		$this->db->trans_complete();
      		if ($this->db->trans_status() === FALSE)
			{
			    $result = FALSE;
			}
			else
			{
				$deleted_data = array('user'=>$this->session->userdata('ID'),'deleted_on'=>date('Y-m-d h:i:s'),'table'=>'chapter','data'=>$rec);
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
	}
?>