<?php
	class Login_model extends CI_Model{

		public function authenticate($email=NULL,$passwd=NULL)
		{
			if(!is_null($passwd)||!is_null($email))
			{
				if(!$this->is_locked())
				{
					$email = $this->db->escape_str($email);
					$query = $this->db->get_where('users',array("Email"=>$email,"Type !="=>"Client"));
					if($query->num_rows() > 0)
					{
						if($query->row()->Password === $passwd)
						{
							$logindata = array(
								'ID' => $query->row()->ID,
								'Name' => $query->row()->Name,
								'Email' => $query->row()->Email,
								'Login_as' => $query->row()->Type,
								'Logged_in' => "TRUE",
							);
							$this->session->set_userdata($logindata);
							$this->session->set_flashdata('alert', true);
							$res['status'] = TRUE;
							$res['type'] = $query->row()->Type;
							$res['ID'] = $query->row()->ID;
							return TRUE;
						}
						else
						{
							return FALSE;
						}
					}
					else {
						return FALSE;
					}
				}
				else {
					if(is_null($email) && !is_null($passwd) && $this->is_locked())
					{
						$email = $this->session->userdata('Email');
						$query = $this->db->get_where('users',array("Email"=>$email));
						if($query->num_rows() > 0)
						{
							if($query->row()->Password === $passwd)
							{
								return TRUE === $this->unlock() ? TRUE : FALSE; 
							}
							else
							{
								return FALSE;
							}
						}
						else
						{
							redirect('Login/logout');
						}
					}
					else
					{
						return FALSE;
					}
				}
			}
		}

	    public function check_login()
	    {
	    	if($this->session->userdata('Logged_in') === "TRUE")
	    	{
	    		return TRUE;
	    	}
	    	else
	    	{
	    		return FALSE;
	    	}	
	    }

	    public function is_locked()
	    {
	    	if($this->session->userdata('Logged_in') === "FALSE")
	    	{
	    		return ($this->session->userdata('Locked') === "TRUE") ? TRUE : FALSE;
	    	}
	    	else
	    	{
	    		return FALSE;
	    	}
	    }

	    private function unlock()
	    {
	    	if($this->is_locked())
	    	{
	    		$this->session->set_userdata('Logged_in','TRUE');
	    		$this->session->set_userdata('Locked','FALSE');
	    		if(($this->session->userdata('Logged_in') === 'TRUE') && ($this->session->userdata('Locked') === 'FALSE'))
	    		{
	    			return TRUE;
	    		}
	    		else
	    		{
	    			return FALSE;
	    		}
	    	}
	    	else
	    	{
	    		return FALSE;
	    	}
	    }

	    public function lock()
	    {
	    	if($this->check_login())
	    	{
	    		$this->session->set_userdata('Logged_in','FALSE');
	    		$this->session->set_userdata('Locked','TRUE');
	    		if(($this->session->userdata('Logged_in') === 'FALSE') && ($this->session->userdata('Locked') === 'TRUE'))
	    		{
	    			return TRUE;
	    		}
	    		else
	    		{
	    			return FALSE;
	    		}
	    	}
	    	else
	    	{
	    		if($this->is_locked())
	    		{
	    			return TRUE;
	    		}
	    		else
	    		{
	    			return FALSE;
	    		}
	    	}
	    }

	    public function logout()
	    {
	    	$this->session->sess_destroy();
	    	return TRUE;
	    }
	}
?>