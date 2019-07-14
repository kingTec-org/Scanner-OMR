<?php
	class Batch_model extends CI_Model
	{
		public function check($id=NULL)
		{
			$org = $this->db->get('batch');
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
				redirect('batch/add/');
			}
		}

		public function add_or_edit()
		{
			$post = $this->input->post(NULL);
			if ($this->form_validation->run('batch') !== FALSE)
			{
				if(empty($post['ID']))
				{
					unset($post['ID']);
					$post['created_by'] = $this->session->userdata('ID');
					$this->db->trans_start();
					$result = $this->db->insert('batch',$post);
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
			 		$result = $this->db->update('batch',$post);
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

		public function get_show_data($ajax = NULL)
		{
			$get_col = (!is_null($ajax)) ? 'ID,title' : 'ID,title,description';
			$this->db->select($get_col);
			$org = $this->db->get('batch');
			if($ajax == NULL)
			{
				if ($org->num_rows() > 0) 
				{
					$data['data'] = $org->result_array();
					foreach ($data['data'] as $key => $value) {
						$data['data'][$key]['Actions'] = '<a class="btn btn-sm btn-primary" href="'.base_url('batch/add/'.$value['ID']).'"><i class="zmdi zmdi-edit"></i> Edit</a> <button class="btn btn-sm btn-danger" onClick="deletef(\''.$value['ID'].'\',\''.base_url('batch/delete').'\')"><i class="zmdi zmdi-delete"></i> Delete</button>';
					}
				}
				else
				{
					$data = array('data'=>array());
				}
			}
			else{
				return $org->result_array();
			}
			return $data;
		}

		public function delete($id = NULL)
		{
			$this->load->helper('file');
			$this->db->trans_start();
			$org = $this->db->get_where('batch',array('ID'=>$id));
			$rec = $org->result_array();
			$this->db->where('ID', $id);
      		$result = $this->db->delete('batch');
      		$this->db->trans_complete();
      		if ($this->db->trans_status() === FALSE)
			{
			    $result = FALSE;
			}
			else
			{
				$deleted_data = array('user'=>$this->session->userdata('ID'),'deleted_on'=>date('Y-m-d h:i:s'),'table'=>'batch','data'=>$rec);
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