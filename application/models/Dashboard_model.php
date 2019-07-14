<?php
	class Dashboard_model extends CI_Model{
	
		public function get_details()
		{
			$p = $this->fetch_model->show('P','count(ID)');
			$data['Pcount'] = $p[0]['count(ID)'];
			$m = $this->fetch_model->show('M','count(ID)');
			$data['Mcount'] = $m[0]['count(ID)'];
			$c = $this->fetch_model->show('C','count(ID)');
			$data['Ccount'] = $c[0]['count(ID)'];
			$v = $this->fetch_model->show('V','count(ID)');
			$data['Vcount'] = $v[0]['count(ID)'];
			$e = $this->fetch_model->show(array('B'=>array('bill_type'=>'Estimate')),'count(ID)');
			$data['Ecount'] = $e[0]['count(ID)'];
			$i = $this->fetch_model->show(array('B'=>array('bill_type'=>'Invoice')),'count(ID)');
			$data['Icount'] = $i[0]['count(ID)'];
			$gt = $this->fetch_model->show(array('B'=>array('bill_type'=>'Invoice')),'sum(grand_total)');
			$data['sum'] = (int)$gt[0]['sum(grand_total)'];
			$skyq = $this->config->item('skyq');
			$tc = $this->fetch_model->show(array('T'=>array('expence_category_ID'=>$skyq['Customer_Credit'])),'sum(amount)');
			$data['total'] = (int)$tc[0]['sum(amount)'];
			$data['balance'] = (int)$data['sum'] - (int)$data['total'];
			$a = $this->fetch_model->show('A','count(ID)');
			$data['Acount'] = $a[0]['count(ID)'];
			// timeline alert
			$start=date('Y-m-d');
			$end=date('Y-m-d', strtotime("+5 days"));
			$data['timeLineAlert']=$this->getAlerts($start,$end);
			return $data;
			
		}

		public function getAlerts($start,$end)
		{
			$allDates=$this->allDatesBetwn($start,$end);
			if ($allDates) {
				foreach ($allDates as $key => $value) {
					// $leadData[$value]=$this->fetch_model->show(array('LT'=> array('DateTime LIKE'=>'%'.$value.'%','cnt_Result '=>'NeedToContact')));
					$value1=$this->date_library->db2date($value,$this->date_library->get_date_format());
					$leadData[$value1]=$this->fetch_model->show(array('LT'=> array('DateTime LIKE'=>'%'.$value.'%','heading_status'=>'Sub')));

					
				}

				if ($leadData) {
					foreach ($leadData as $key => $value) {
						foreach ($value as $k => $v) {
							// print_r($leadData[$key][$k]['lead_ID']);
							if (strpos($v['lead_ID'], 'LDSK') !== false) {
	 						 	$name=$this->fetch_model->show(array('LD'=>array('ID'=>$v['lead_ID'])));
	 						 	if ($name) {
		 						 	$leadData[$key][$k]['lead_ID']=$name[0]['Name']." <span class='text-danger'>(LEAD)</span>";
		 						 }
		 						 else
		 						 {
		 						 	$leadData[$key][$k]['lead_ID']='-NA-';
		 						 }
	 						 }
	 						 else
	 						 {
	 						 	$name=$this->fetch_model->show(array('C'=>array('ID'=>$v['lead_ID'])));
	 						 	if ($name) {
		 						 	$leadData[$key][$k]['lead_ID']=$name[0]['f_Name'];
		 						 	// $leadData[$key][$k]['lead_ID']=$name[0]['name'];
		 						 }
		 						 else
		 						 {
		 						 	$leadData[$key][$k]['lead_ID']='-NA-';
		 						 }
	 						 }

	 						 if ($v['cnt_Result']==='NeedToContact') {
	 						 	$leadData[$key][$k]['cnt_Result']='Pending.....';
	 						 }
	 						 else
	 						 {
	 						 	$leadData[$key][$k]['cnt_Result']='<span class="label label-primary">Completed</span>';
	 						 }
						}
					}
				}
				return $leadData;
				// return array_reverse($leadData);
			}
			// $leadData=$this->fetch_model->show(array('LT'=> array('DateTime >='=>$start,'DateTime <='=>$end,'cnt_Result '=>'NeedToContact',false)));
			// var_dump($leadData);
			// if (strpos($v['lead_ID'], 'LDSK') !== false) {
	 					// 	 	$name=$this->fetch_model->show(array('LD'=>array('ID'=>$v['lead_ID'])));
	 					// 	 	if ($name) {
		 				// 		 	$data[$k]['lead_ID']=$name[0]['Name']." <span class='text-danger'>(LEAD)</span>";
		 				// 		 }
		 				// 		 else
		 				// 		 {
		 				// 		 	$data[$k]['lead_ID']='-NA-';
		 				// 		 }
	 					// 	 }
	 					// 	 else
	 					// 	 {
	 					// 	 	$name=$this->fetch_model->show(array('C'=>array('ID'=>$v['lead_ID'])));
	 					// 	 	if ($name) {
		 				// 		 	$data[$k]['lead_ID']=$name[0]['name'];
		 				// 		 }
		 				// 		 else
		 				// 		 {
		 				// 		 	$data[$k]['lead_ID']='-NA-';
		 				// 		 }
	 					// 	 }
		}

		public function allDatesBetwn($strDateFrom,$strDateTo)
		{
			 $aryRange=array();
		    $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
		    $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

		    if ($iDateTo>=$iDateFrom)
		    {
		        array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
		        while ($iDateFrom<$iDateTo)
		        {
		            $iDateFrom+=86400; // add 24 hours
		            array_push($aryRange,date('Y-m-d',$iDateFrom));
		        }
		    }
		    return $aryRange;
		}

		public function get_details2()
		{
			$data['customer'] = $this->fetch_model->show('C',array('ID','f_Name'));
			// $data['customer'] = $this->fetch_model->show('C',array('ID','name'));
			$data['Product_Model'] = @$this->get_product_and_model();
			return $data;
		}

		public function get_product_and_model()
		{
			$this->load->model('product_model');
			$products = $this->fetch_model->show('P');
			$new_array = array();
			foreach ($products as $key => $product) {
				$product_det = $this->product_model->get_details($product['ID']);
				foreach ($product_det['List']['Models'] as $key1 => $model) {
					$new_array[$product['ID'].'-'.$model['ID']] = $product['name'].' - '.$model['model_name'];	
				}
			}
			return $new_array;
		}

		public function show_invoice($customer_ID = NULL)
		{
			$data = $this->fetch_model->show(array('B'=>array('customer_ID'=>$customer_ID,'bill_type'=>'Invoice')),array('ID','bill_number','grand_total'));
			return $data;
		}

		public function show_invoice_data($ID = NULL,$month_year = NULL)
		{
			if (strpos($ID,'ST') !== false) 
			{
    			$c2 = $this->fetch_model->show(array('T'=>array('referance_Name LIKE'=>'%'.$ID.'%')),'sum(amount)');
				$data['collection'] = $c2[0]['sum(amount)'];
				$t2 = $this->fetch_model->show(array('FR'=>array('Student_ID'=>$ID)),'total_fees');
				$data['grandtotal'] = $t2[0]['total_fees'];
			}
			else if (strpos($ID,'BA') !== false) 
			{
				$c2 = $this->fetch_model->show(array('T'=>array('referance_Name LIKE'=>'%'.$ID.'%')),'sum(amount)');
				$data['collection'] = $c2[0]['sum(amount)'];
				$t2 = $this->fetch_model->show(array('FR'=>array('Student_ID'=>$ID)),'total_fees');
				$data['grandtotal'] = $t2[0]['total_fees'];	
			}
			else
			{
				$c2 = $this->fetch_model->show(array('T'=>array('referance_Name LIKE'=>'%'.$ID.'%','month_year'=>$month_year)),array('sum(amount)','salary'));
				if (empty($c2[0]['sum(amount)'])) 
				{
					$data['collection'] = $c2[0]['sum(amount)'];
				}
				$data['collection'] = $c2[0]['sum(amount)'];
				$t2 = $this->fetch_model->show(array('US'=>array('ID'=>$ID)),'salary');
				if (empty($c2[0]['sum(amount)'])) 
				{
					$data['grandtotal'] = $t2[0]['salary'];	
				}
				else
				{
					$data['grandtotal'] = $c2[0]['salary'];		
				}
			}
			return $data;
		}

		public function timeLineAlert($value='')
		{
			$start=date('Y-m-d H:i:s');
			if ($this->data['Login']['Login_as']==='Admin') {
				$data1=$this->fetch_model->show(array('LT'=> array('DateTime >='=>$start)));
				if ($data1) {

					$new = array();
					$data = array();
					foreach ($data1 as $value)
					{
					    $new[$value['Added_by']] = $value;
					}
					$name='';
					foreach ($new as $key => $value) {
						$uData=$this->fetch_model->show(array('US'=> array('ID'=>$value['Added_by'])));
						$name .= $uData[0]['Name'].', ';

					}
					$data['Added_by']=trim($name,', ');
					$data['totalCount']=count($data1);

				}
				else
				{
					$data=null;
				}
			}
			else
			{
				$data=$this->fetch_model->show(array('LT'=> array('DateTime >='=>$start,'Added_by'=>$this->data['Login']['ID'])));
			}
			if ($data) {
				return $data;
			}
			else
			{
				return false;
			}
		}

		function SameKeyData ($array) {
		  $result = array();
		  foreach ($array as $sub) {
		    foreach ($sub as $k => $v) {
		      $result[$k][] = $v;
		    }
		  }
		  return $result;
		}

		public function contacted()
		{
			// $_POST['cnt_Result']='Contacted';
			// $this->load->model('form_model');
			// $t_add =$this->form_model->edit(array("table"=>"LT","columns"=>$_POST,'where'=>array('ID'=>$_POST['ID'])));
			// return $t_add;
			if (strpos($_POST['ID'], 'TK')!==false) {
				$this->load->model('form_model');
				$_POST['end_time'] = $this->date_library->date2db($_POST['end_time'],$this->date_library->get_date_format());
				$_POST['end_time'] = $_POST['end_time'].' '.date("H:i:s", strtotime($_POST['eTime']));
				unset($_POST['eTime']);
				$t_add =$this->form_model->edit(array("table"=>"TK","columns"=>array('end_time'=>$_POST['end_time'],'tStatus'=>'Pending Approval'),'where'=>array('ID'=>$_POST['ID'])));
				/*$data=$this->fetch_model->show(array('TKS'=> array('task_ID'=>$_POST['ID'],'tStatus'=>'Inprocess')));
				$taskDetail=$this->fetch_model->show(array('TK'=> array('ID'=>$_POST['ID'])));
				if ($data) {
					$subTAsks='';
					$x=1;
					foreach ($data as $key => $value) {
						if ($value['type']==='text') {
							$subTAsks .= $x.'. '.$value['sub_task'].'<br>';
						}
						$t_add =$this->form_model->edit(array("table"=>"TKS","columns"=>array('tStatus'=>'Completed'),'where'=>array('ID'=>$value['ID'])));
						$x++;
					}
					$desc='';
					$desc .= '<h4>'.$taskDetail[0]['title'].'</h4>';
					if (!empty($taskDetail[0]['description'])) {
						$desc .='<h5>'.$taskDetail[0]['description'].'</h5>';
					}
					else
					{
						$desc .='<code><h5>No description present</h5></code>';
					}
					if ($data) {
						$desc .=$subTAsks;
					}
					else
					{
						$desc .='<code><h5>No Subtask present</h5></code>';
					}
					
					// $desc=""
					$t_add1 = $this->form_model->add(array("table"=>"LT","columns"=>array('title'=>'<a target="_blank" href='.base_url('Team/View/'.$this->str_function_library->call('fr>EP>employee_ID:ID=`'.$this->session_library->get_session_data('employeeID').'`')).'><code>'.$this->str_function_library->call('fr>EP>Name:ID=`'.$this->session_library->get_session_data('employeeID').'`').'</code> Completed his Task was assigned on '.$taskDetail[0]['Date'].'</span></a>','heading_status'=>'Main','lead_ID'=>$this->session_library->get_session_data('employeeID'),'description'=>$desc)));
					// var_dump($t_add1);
					return $t_add;
				}
				else
				{
					$desc='';
					$desc .= '<h4>'.$taskDetail[0]['title'].'</h4>';
					if (!empty($taskDetail[0]['description'])) {
						$desc .='<h5>'.$taskDetail[0]['description'].'</h5>';
					}
					else
					{
						$desc .='<code><h5>No description present</h5></code>';
					}
					$t_add1 = $this->form_model->add(array("table"=>"LT","columns"=>array('title'=>'<a target="_blank" href='.base_url('Team/View/'.$this->str_function_library->call('fr>EP>employee_ID:ID=`'.$this->session_library->get_session_data('employeeID').'`')).'><code>'.$this->str_function_library->call('fr>EP>Name:ID=`'.$this->session_library->get_session_data('employeeID').'`').'</code> Completed his Task was assigned on '.$taskDetail[0]['Date'].'</span></a>','heading_status'=>'Main','lead_ID'=>$this->session_library->get_session_data('employeeID'),'description'=>$desc)));
					return $t_add;
				}*/
			}
			else
			{
				$_POST['cnt_Result']='Contacted';
				$this->load->model('form_model');
				$t_add =$this->form_model->edit(array("table"=>"LT","columns"=>$_POST,'where'=>array('ID'=>$_POST['ID'])));
			}
			return $t_add;
		}

		public function getTimeline($start,$end)
		{
			// print_r($start);
			// print_r($end);
			if ($start===$end) {
				// $data = $this->fetch_model->show(array('LT'=> array('DateTime LIKE'=>'%'.$start.'%')),'*',array('ORDER'=>array('DateTime','ASC')));
				$data = $this->fetch_model->show(array('LT'=> array('Status'=>'A')),'*',array('ORDER'=>array('DateTime','ASC')));
		 		$byAddedon=$this->fetch_model->show(array('LT'=> array('DateTime'=>NULL,'Added_on LIKE'=>'%'.$start.'%')),'*',array('ORDER'=>array('Added_on','ASC')));
			}
			else
			{
				$data = $this->fetch_model->show(array('LT'=> array('DateTime >='=>$start,'DateTime <='=>$end)),'*',array('ORDER'=>array('DateTime','ASC')));
		 		$byAddedon=$this->fetch_model->show(array('LT'=> array('DateTime'=>NULL,'Added_on >='=>$start,'Added_on <='=>$end)),'*',array('ORDER'=>array('Added_on','ASC')));
			}
		 	$timeline = array_merge($data, $byAddedon);
			// usort($timeline, array($this,'cmp'));
			if ($timeline) {
				foreach ($timeline as $key => $value) {
					$meetData = $this->fetch_model->show(array("LMT"=>array('ID'=>$value['meet_type'])),array('name','icon','ID'));
					if ($meetData) {
						$timeline[$key]['meet_type']=$meetData[0];
					}
					else
					{
						$timeline[$key]['meet_type']=array('icon'=>'fa-magnet');
					}
					
					if ($value['cnt_Result']==='NeedToContact') {
						if ($value['isContact']==='Yes') {
							if ($value['DateTime']<date('Y-m-d H:s:m')) {
								$timeline[$key]['isContacted']="No";
							}
							else
							{
								$timeline[$key]['isContacted']="Yes";
							}
						}
						else
						{
							$timeline[$key]['isContacted']="Yes";
						}
					}
					else
					{
						$timeline[$key]['isContacted']="Yes";
					}
					if ($value['DateTime']===NULL) {
						$timeline[$key]['mainDateTime']=$value['Added_on'];
						$timeline[$key]['showDateTime']=$this->date_library->db2date($value['Added_on'],$this->date_library->get_date_format());
					}
					else
					{
						$timeline[$key]['mainDateTime']=$value['DateTime'];
						$timeline[$key]['showDateTime']=$this->date_library->db2date($value['DateTime'],$this->date_library->get_date_format());
					}

					if ($value['heading_status']==='Sub') {
						if (!empty($value['lead_ID'])) {
							if (strpos($value['lead_ID'],"VSK")!==false) {
								$ref=$this->fetch_model->show(array("V"=>array('ID'=>$value['lead_ID'])),array('name'));
								$timeline[$key]['refCus']=' '.$ref[0]['name'].' <span class="text-warning">(Vendor)</span>';
							}
							else if (strpos($value['lead_ID'],"CSK")!==false) {
								$ref=$this->fetch_model->show(array("C"=>array('ID'=>$value['lead_ID'])),array('name'));
								$timeline[$key]['refCus']=' '.$ref[0]['name'].' <span class="text-warning">(Customer)</span>';
							}
							else if (strpos($value['lead_ID'],"LDSK")!==false) {
								$ref=$this->fetch_model->show(array("LD"=>array('ID'=>$value['lead_ID'])),array('Name'));
								$timeline[$key]['refCus']=' '.$ref[0]['Name'].' <span class="text-warning">(LEAD)</span>';
							}
							else
							{
								$timeline[$key]['refCus']='';
							}
						}
						else
						{
							$timeline[$key]['refCus']='';
						}
					}
					else
					{
						$timeline[$key]['refCus']='';
					}
					
					if ($value['delete_status']==='D') {
						$timeline[$key]['title']=strip_tags($value['title']);
					}
					else
					{
						$timeline[$key]['title']=$value['title'];
					}


					// var_dump($value['DateTime']);
				}
				usort($timeline, array($this,'cmp'));
				return array_reverse($timeline);
			}
			else
			{
				return false;
			}


		}

		function cmp($a, $b){
		    $ad = strtotime($a['mainDateTime']);
		    $bd = strtotime($b['mainDateTime']);
		    return ($ad-$bd);
		}

		public function cmp_date($a,$b){
		    return strtotime($a['start_date'])<strtotime($b['start_time'])?1:-1;
		}

		public function getTodaysTask($branch_ID = NULL, $start = null, $end = null,$employee_ID=null)
		{
			$taskData = false;
			if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
				$employees = $this->fetch_model->show('US');
				foreach ($employees as $key => $value) {
					$data = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$branch_ID,'tStatus'=>'Inprocess','assignTo LIKE'=>'%'.$value['ID'].'%')));
					if(($data != NULL) && !empty($data) && ($data != FALSE))
					{
						foreach ($data as $ky => $vl) {
							$vl['assignTo'] = $value['ID'];
							// var_dump($employee_ID);
							// var_dump($vl);
							if($employee_ID != 'all')
							{
								 // var_dump($value['ID']);
								if($value['ID'] == $employee_ID)
								{
									$taskData[] = $vl;
								}

							}
							else{
								$taskData[] = $vl;
							}					
						}
					}
				}
				// print_r($taskData);
			}
			else if($this->data['Login']['Login_as'] === 'DSSK10000009')
			{
				$tasks = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
				$classes = $this->fetch_model->show(array('CL'=> array('start_date'=>date('Y-m-d'),'professor_ID'=>$this->session_library->get_session_data('ID'))));
				$taskData = array_merge($tasks,$classes);
				usort($taskData, array($this, 'cmp_date'));
			}
			else
			{
				$taskData = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
			}

			if ($taskData) {
				foreach ($taskData as $key => $value) {
					$ID = explode('SK', $value['ID']);
					if($ID[0] === 'TK')
					{
						$taskData[$key]['type'] = 'task';
					}
					else
					{
						$taskData[$key]['type'] = 'class';
					}
					if (!empty($value['copiedID'])) {
						$copyID = explode(',', $value['copiedID']);
						$copyDates = '';
						foreach ($copyID as $k1 => $v1) {
							$copyData = $this->fetch_model->show(array('TK'=> array('ID'=>$v1)));
							$copyDates .= $copyData[0]['Date'].',';
						}
						$taskData[$key]['copiedWholeTask'] = "This task is copied to ".trim($copyDates,',');
					}
					else
					{
						$taskData[$key]['copiedWholeTask'] = '';
					}
					if ($value['description'] === null) {
						$taskData[$key]['description'] = "<code>No Description Present</code>";
					}
						/*$subTaskData=$this->fetch_model->show(array('TKS'=> array('task_ID'=>$value['ID'])));
						if ($subTaskData) {
							$subTitle='';
							$x=1;
							$file=array();
							foreach ($subTaskData as $k => $v) {
								if ($v['type']==='text') {
									if ($v['tStatus']==='Copied') {
										$assTask=$this->fetch_model->show(array('TK'=> array('ID'=>$v['copiedTo'])),array('>>emp:>fr>EP>Name:ID=^assignTo^'));
										$subTitle .= '<strike>'.$x.'. '.$v['sub_task'].'</strike> <B class="text-success">Assign Task To '.$assTask[0]['emp'].'</B><br>';
									}
									else
									{
										$subTitle .= $x.'. '.$v['sub_task'].'<br>';
									}
									$x++;
								}
								else
								{
									$file=array($x=>$v['sub_task']);
								}
							}
							$taskData[$key]['subTaskTitle']=$subTitle;
						}
						else
						{
							$taskData[$key]['subTaskTitle']="<code>No Subtitles Present</code>";
						}*/
				}
				if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
					$taskData1 = array();
					// var_dump($taskData);
					foreach ($taskData as $key => $value) {
						if(array_key_exists($value['assignTo'], $taskData1))
						{
							if (!empty($value['assignTo'])) {
								$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$value['assignTo'])),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
								$value['assignTo'] = '<br><img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
							}
							else
							{
								$value['assignTo'] = "<br><b>Not Assigned Yet<b>";
							}
							$taskData1[$value['assignTo']][] = $value;
						}
						else{
							if (!empty($value['assignTo'])) {
								$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$value['assignTo'])),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
								$value['assignTo'] = '<br><img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
							}
							else
							{
								$value['assignTo'] = "<br><b>Not Assigned Yet<b>";
							}
							$taskData1[$value['assignTo']][] = $value;
						}
					}
					$taskData1 = array('type'=>'Admin','data'=>$taskData1);
					return $taskData1;
				}
				else
				{
					$taskData=array('type'=>'team','data'=>$taskData);
					return $taskData;
				}
			}
			else
			{
				return false;
			}
		}

		public function type_wise()
		{
			if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
				if(($_POST['type_ID'] == NULL) || empty($_POST['type_ID']) || ($_POST['type_ID'] == 'all'))
				{
					$tasks = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$_POST['branch_ID'],'tStatus'=>'Inprocess')));
					$calender = $this->fetch_model->show(array('CTK'=>array('branch_ID'=>$_POST['branch_ID'])));
				}
				else
				{
					$tasks = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$_POST['branch_ID'],'tStatus'=>'Inprocess','task_type_ID LIKE'=>'%'.$_POST['type_ID'].'%')));
					$calender = $this->fetch_model->show(array('CTK'=>array('branch_ID'=>$_POST['branch_ID'],'task_type_ID LIKE'=>'%'.$_POST['type_ID'].'%')));
				}
				$taskData = array_merge($tasks,$calender);
			}
			else if($this->data['Login']['Login_as'] === 'DSSK10000009')
			{
				if(($_POST['type_ID'] == NULL) || empty($_POST['type_ID']) || ($_POST['type_ID'] == 'All'))
				{
					$tasks = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
					$calender = $this->fetch_model->show(array('CTK'=> array('Added_by'=>$this->session_library->get_session_data('ID'))));
				}
				else
				{
					$tasks = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess','task_type_ID LIKE'=>'%'.$_POST['type_ID'].'%')));
					$calender = $this->fetch_model->show(array('CTK'=> array('Added_by'=>$this->session_library->get_session_data('ID'),'task_type_ID'=>$_POST['type_ID'])));
				}
				$classes = $this->fetch_model->show(array('CL'=> array('start_date'=>date('Y-m-d'),'professor_ID'=>$this->session_library->get_session_data('ID'))));
				$tts = array_merge($tasks,$calender);
				$taskData = array_merge($tts,$calender);
				usort($taskData, array($this, 'cmp_date'));
			}
			else
			{
				if(($_POST['type_ID'] == NULL) || empty($_POST['type_ID']) || ($_POST['type_ID'] == 'All'))
				{
					$tasks = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
					$calender = $this->fetch_model->show(array('CTK'=> array('Added_by'=>$this->session_library->get_session_data('ID'))));
				}
				else
				{
					$task = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess','task_type_ID LIKE'=>'%'.$_POST['type_ID'].'%')));
					$calender = $this->fetch_model->show(array('CTK'=> array('Added_by'=>$this->session_library->get_session_data('ID'),'task_type_ID'=>$_POST['type_ID'])));
				}
				$taskData = array_merge($tasks,$classes);
			}
			if ($taskData) {
				foreach ($taskData as $key => $value) {
					$ID = explode('SK', $value['ID']);
					if($ID[0] === 'TK')
					{
						$taskData[$key]['type'] = 'task';
					}
					else if($ID[0] === 'CTK')
					{
						$taskData[$key]['type'] = 'calender';
					}
					else
					{
						$taskData[$key]['type'] = 'class';
					}
					if (!empty($value['copiedID'])) {
						$copyID = explode(',', $value['copiedID']);
						$copyDates = '';
						foreach ($copyID as $k1 => $v1) {
							$copyData = $this->fetch_model->show(array('TK'=> array('ID'=>$v1)));
							$copyDates .= $copyData[0]['Date'].',';
						}
						$taskData[$key]['copiedWholeTask'] = "This task is copied to ".trim($copyDates,',');
					}
					else
					{
						$taskData[$key]['copiedWholeTask'] = '';
					}
					if ($value['description'] === null) {
						$taskData[$key]['description'] = "<code>No Description Present</code>";
					}
				}
				if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
					$taskData1 = array();
					foreach ($taskData as $key => $value) {
						if($value['task_type_ID'] == NULL)
						{
							$value['assignTo'] = "<br><b class='text-danger'>Not department Selected.</b>";
						}
						else
						{
							$task_types = strpos($value['task_type_ID'], ',');
							if($task_types == TRUE)
							{
								$value['assignTo'] = '<br><b>';
								$tasks_t = explode(',', $value['task_type_ID']);
								foreach ($tasks_t as $keyt => $valuet) {
									$value['assignTo'] .= ' '.$this->str_function_library->call('fr>TT>title:ID=`'.$valuet.'`').',';
								}
								$value['assignTo'] = rtrim($value['assignTo'],",");
								$value['assignTo'] .= '</b>';
							}
							else
							{
								$value['assignTo'] = "<br><b>".$this->str_function_library->call('fr>TT>title:ID=`'.$value['task_type_ID'].'`').'</b>';
							}
						}
						$taskData1[$value['assignTo']][] = $value;
					}
					$taskData1 = array('type'=>'Admin','data'=>$taskData1);
					return $taskData1;
				}
				else
				{
					$taskData = array('type'=>'team','data'=>$taskData);
					return $taskData;
				}
			}
			else
			{
				return false;
			}
		}

		public function date_wise()
		{
			if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
				if(($_POST['date'] == NULL) || empty($_POST['date']) || ($_POST['date'] == 'all'))
				{
					$taskData = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$_POST['branch_ID'],'tStatus'=>'Inprocess')));
				}
				else
				{
					$dates = explode(' - ', $_POST['date']);
					$start = date('Y-m-d H:i:s' ,strtotime($dates[0]));
					$end = date('Y-m-d 23:59:59' ,strtotime($dates[1]));
					$taskData = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$_POST['branch_ID'],'tStatus'=>'Inprocess','start_time >='=>$start,'start_time <='=>$end)));
				}
			}
			else if($this->data['Login']['Login_as'] === 'DSSK10000009')
			{
				if(($_POST['date'] == NULL) || empty($_POST['date']) || ($_POST['date'] == 'All'))
				{
					$tasks = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
				}
				else
				{
					$dates = explode(' - ', $_POST['date']);
					$start = date('Y-m-d H:i:s' ,strtotime($dates[0]));
					$end = date('Y-m-d H:i:s' ,strtotime($dates[1]));
					$taskData = $this->fetch_model->show(array('TK'=>array('branch_ID'=>$_POST['branch_ID'],'tStatus'=>'Inprocess','start_time >='=>$start,'start_time <='=>$end)));
				}
				$classes = $this->fetch_model->show(array('CL'=> array('start_date'=>date('Y-m-d'),'professor_ID'=>$this->session_library->get_session_data('ID'))));
				$taskData = array_merge($tasks,$classes);
				usort($taskData, array($this, 'cmp_date'));
			}
			else
			{
				if(($_POST['date'] == NULL) || empty($_POST['date']) || ($_POST['date'] == 'All'))
				{
					$taskData = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess')));
				}
				else
				{
					$_POST['date'] = date('Y-m-d H:i:s' ,strtotime($_POST['date']));
					$taskData = $this->fetch_model->show(array('TK'=> array('assignTo'=>$this->session_library->get_session_data('ID'),'tStatus'=>'Inprocess','start_time <='=>$_POST['date'])));
				}
			}
			if ($taskData) {
				foreach ($taskData as $key => $value) {
					$ID = explode('SK', $value['ID']);
					if($ID[0] === 'TK')
					{
						$taskData[$key]['type'] = 'task';
					}
					else
					{
						$taskData[$key]['type'] = 'class';
					}
					if (!empty($value['copiedID'])) {
						$copyID = explode(',', $value['copiedID']);
						$copyDates = '';
						foreach ($copyID as $k1 => $v1) {
							$copyData = $this->fetch_model->show(array('TK'=> array('ID'=>$v1)));
							$copyDates .= $copyData[0]['Date'].',';
						}
						$taskData[$key]['copiedWholeTask'] = "This task is copied to ".trim($copyDates,',');
					}
					else
					{
						$taskData[$key]['copiedWholeTask'] = '';
					}
					if ($value['description'] === null) {
						$taskData[$key]['description'] = "<code>No Description Present</code>";
					}
				}
				if ($this->data['Login']['Login_as'] === 'DSSK10000001') {
					$taskData1 = array();
					foreach ($taskData as $key => $value) {
						if(array_key_exists($value['assignTo'], $taskData1))
						{
							if (!empty($value['assignTo'])) {
								$emps = strpos($value['assignTo'], ',');
								if($emps == TRUE)
								{
									$value['assignTo'] = '';
									foreach ($emps as $keye => $valuee) {
										$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$valuee)),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
										$value['assignTo'] .= '<img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
										$img .= '<img alt="member" class="img-circle" src="'.base_url($epmData[0]['emp']).'">';
										$name .= $epmData[0]['Name'].'.';
									}
									$img = rtrim($img,",");
									$value['img'] = $img;
									$value['name'] = $name;
								}
								else
								{
									$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$value['assignTo'])),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
									$value['assignTo'] = '<img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
									$value['img'] = '<img alt="member" class="img-circle" src="'.base_url($epmData[0]['emp']).'">';
									$value['name'] = $epmData[0]['Name'];
								}
							}
							else
							{
								$value['assignTo'] = "<b class='text-danger'>Not Assigned Yet<b>";
							}
							$taskData1[$value['assignTo']][] = $value;
						}
						else{
							if (!empty($value['assignTo'])) {
								$emps = strpos($value['assignTo'], ',');
								$img = '';
								$name = '';
								if($emps == TRUE)
								{
									$emps = explode(',', $value['assignTo']);
									$value['assignTo'] = '';
									foreach ($emps as $keye => $valuee) {
										$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$valuee)),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
										$value['assignTo'] .= '<img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
										$img .= '<img alt="member" class="img-circle" src="'.base_url($epmData[0]['emp']).'">';
										$name .= $epmData[0]['Name'].',';
									}
									$img = rtrim($img,",");
									$value['img'] = $img;
									$value['name'] = $name;
								}
								else
								{
									$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$value['assignTo'])),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
									$value['assignTo'] = '<img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';
									$value['img'] = '<img alt="member" class="img-circle" src="'.base_url($epmData[0]['emp']).'">';
									$value['name'] = $epmData[0]['Name'];
								}
							}
							else
							{
								$value['assignTo'] = "<br><b>Not Assigned Yet<b>";
							}
							$value['start_time'] = date('Y-m-d H:i A',strtotime($value['start_time']));
							$value['expected_end_time'] = date('Y-m-d H:i A',strtotime($value['expected_end_time']));
							$taskData1[$value['assignTo']][] = $value;
						}
					}
					$taskData1 = array('type'=>'Admin','data'=>$taskData1);
					return $taskData1;
				}
				else
				{
					$taskData = array('type'=>'team','data'=>$taskData);
					return $taskData;
				}
			}
			else
			{
				return false;
			}
		}

		public function waiting_approval()
		{
			// print_r($this->data['Login']['ID']);
			$tbl = $this->db_library->get_tbl('US');
			$this->db->where('seniority1_ID',$this->data['Login']['ID']);
			$this->db->or_where('seniority2_ID',$this->data['Login']['ID']);
			$this->db->or_where('seniority3_ID',$this->data['Login']['ID']);
			$query = $this->db->get($tbl);
			if($query->num_rows() > 0)
			{	
				$user_list = array();
				$users = $query->result_array();
				// var_dump($users);
				foreach ($users as $key => $user) {
				// 	$user_list[] = $user['ID'];
				// }
				// // $all_users = explode(',', $user_list);
				// // var_dump($all_users);
				// foreach ($user_list as $userss) {

					$sh = $this->fetch_model->show(array('TK'=>array('assignTo LIKE '=>'%'.$user['ID'].'%','tStatus'=>'Pending Approval','ratings NOT LIKE '=>'%'.$this->data['Login']['ID'].'%')));
					// var_dump($user['ID']);
					if(!empty($sh))
					{
						$show[] = $sh;//$this->fetch_model->show(array('TK'=>array('assignTo LIKE '=>'%'.$user['ID'].'%','tStatus'=>'Pending Approval','ratings NOT LIKE '=>'%'.$this->data['Login']['ID'].'%')));
					}

				}
				$new_arr = array();
				if(!empty($show))
				{
					foreach ($show as $key1 => $val1) {
						if(!empty($val1))
						{
							foreach ($val1 as $key2 => $value2) {
								if(!in_array($value2,$new_arr))
								{
									$new_arr[] = $value2;
								}
							}
						}
					}
				}
				// var_dump($new_arr);
				if(!empty($new_arr))
				{
					foreach ($new_arr as $key => $value) {
						$assignedto = explode(',',$value['assignTo']);
						unset($new_arr[$key]['assignTo']);
						$new_arr[$key]['assignTo'] = '';
						foreach ($assignedto as $assignto) {
							$epmData = $this->fetch_model->show(array('US'=> array('ID'=>$assignto)),array('>>emp:>fr>SS>path:ID=^Image_ID^','Name'));
							$new_arr[$key]['assignTo'] .= '<br><img src="'.base_url($epmData[0]['emp']).'" class="crlwName"> <b>'.$epmData[0]['Name'].'</b>';					
						}
					}
				}
				return $new_arr;
			}
			else{
				return array();
			}
		}

		function change_password($old=NULL,$new=NULL,$confirm=NULL)
		{
			if(!is_null($old) && !is_null($new) && !is_null($confirm))
			{
				if(!empty($old) && !empty($new) && !empty($confirm))
				{	
					// $this->db->select('Password');
					$query = $this->db->get_where('users',array("Status"=>"A","Id"=>$this->session->userdata('Id')));
					/*var_dump($query->num_rows());
					var_dump($this->session->userdata('Id'));*/
					if($query->num_rows() > 0)
					{
						$result = $query->row();
						$password = $result->Password;
						if($old===$password && $new === $confirm)
			            { 
			            	$update = array(
			            		'Password' => $confirm
			            	);
			            	$this->db->where(array('Id'=>$this->session->userdata('Id'),'Status'=>'A'));
							$this->db->update('users',$update); 
							return TRUE;
			            }
			             else if($old!==$password)
			            {
			            	return array('er_old' => 'Please enter valid current password');
			            }
			            else if($new !== $confirm)
			            {
			            	return array('er_new' => 'New password does not match to Confirm password field.');
			            }
				         else 
				        {
			             	return array('er_old' => 'Please enter all fields');
			            }	
			        }
			        else
			        {
			        	return array('er_old' => 'Please enter valid current password.'); 
			        } 
			    }
			    else{
			    	return array('er_old' => 'Please enter all fields');
			    }    
		    }       
		}
}
?>
