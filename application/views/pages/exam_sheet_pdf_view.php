<!DOCTYPE html>
<html>
<head>
	<!-- <title>OMR ANSWER SHEET PDF</title> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/pdf.css'); ?>">
</head>
<body>

<?php 
		$r_char = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O');
		$c_char = array('P','Q','R','S','T','U','V','W','X','Y','Z');
		$total_subjects = count($data['exam']['z_sections']);
		$z = 0;
		foreach($data['students'] as $students){ 
?>
		<div style="height:100% !important;">
			<div style="height:10% !important;border:1px solid black;margin-bottom: 10px;padding-right:10px;padding-left: 10px;"><?php print_r($students); ?></div>
			<div style="height:85% !important;width:99.7%;border:1px solid black;">
<?php 		$i = 0;
			foreach ($data['exam']['z_sections'] as $ks=>$subject_sections) {
?>
				<div style="border-spacing: 0; text-align:center;float:left;border-bottom:1px solid black;width: <?php echo 99.5/$total_subjects; ?>%;<?php echo ($i != 0) ? 'border-left:1px solid black;' : '';?>">
					<div class="subject_title"><?php echo $ks; ?></div>

<?php 				foreach ($subject_sections as $ssk=>$ssv) {
?>						<div class="section_title"><?php echo $ssv['section']; ?></div>
						<div class="section_title">
<?php 					switch($ssv['z_questions'][0]['qn_type'])
						{
							case 'single':
							case 'multiple':
								$count = round(12*2/(($ssv['z_questions'][0]['length']+1)*$total_subjects));
								$cnt = count($ssv['z_questions'])/$count;
								$a = 1;
								for($x=0;$x<$count;$x++)
								{
?>
									<div style="float: left;width:<?php echo 100/$count; ?>%">
<?php
									$b = $a + $cnt-1;
									for($c=$a;$c<=$b;$c++)
									{
?>										<ul class="ul-hoz">
											<li class="title"><?php echo $c; ?>. </li>
<?php 									for ($l=0; $l < $ssv['z_questions'][0]['length']; $l++) { 
?>											<!-- <li class="circle"><?php echo $r_char[$l]; ?></li> -->
											<li class="circle"><img class="img-responsive" src="<?php echo base_url('img/'.$r_char[$l].'.jpg'); ?>"></li>
											<li class="spacing"> &nbsp;</li>
<?php 									}
?>										</ul>
<?php								}
									$a = $a + $cnt;
?>									</div>
<?php							}
								break;
							case 'numeric':
								$count = round(12/($ssv['z_questions'][0]['length']));
								$cnt = round(count($ssv['z_questions'])/$count);
								$a = 1;
								for($x=0;$x<$count;$x++)
								{
?>
									<div style="float: left;width:<?php echo 100/$count; ?>%">

<?php
									$b = $a + $cnt-1;
									for($c=$a;$c<=$b;$c++)
									{
										if(array_key_exists($x,$ssv['z_questions']))
										{
											if($ssv['z_questions'][$x]['length']!=1)
											{
?>											<ul class="ul-hoz">
												<li class="title"><?php echo $c; ?>. </li>
												<li class="spacing"> &nbsp;</li>
												<li class="spacing"> &nbsp;</li>
												<li class="spacing"> &nbsp;</li>
												<li class="circle"><img class="img-responsive" src="<?php echo base_url('img/+.jpg'); ?>"></li>
												<!-- <li class="circle">+</li> -->
												<li class="spacing"> &nbsp;</li>
												<li class="circle"><img class="img-responsive" src="<?php echo base_url('img/-.jpg'); ?>"></li>
												<!-- <li class="circle">-</li> -->
											</ul>
<?php 										}
											else{
?>											<ul class="ul-hoz">
												<li class="title"><?php echo $c; ?>. </li>
											</ul>											
<?php										}						
										$num_count = ($ssv['z_questions'][$x]['length']==1) ? 10 : 11;
										$dimension = array($num_count,$ssv['z_questions'][$x]['length']);
										for($d1=0;$d1<$dimension[0];$d1++)
										{
		?>
										<ul class="ul-hoz">

<?php 										for($d2=0; $d2<$dimension[1]; $d2++)
											{
?>												<?php echo ($d2==0) ? '' : '<li class="spacing"> &nbsp;</li>'; ?>
												<li class="circle"><img class="img-responsive" src="<?php echo ($d1==10) ? base_url('img/..jpg') : base_url('img/'.$d1.'.jpg'); ?>"></li>
												<!-- <li class="circle"><?php echo ($d1==10) ? '.' : $d1; ?></li> -->
<?php 										}
?>										</ul>											
<?php									}
										}
									}
									$a = $a + $cnt;
?>									</div>
<?php							}
								break;
							case 'matrix':
								$count = round(12/($ssv['z_questions'][0]['length']+1));
								// print_r($count);
								$cnt = count($ssv['z_questions'])/$count;
								$a = 1;
								for($x=0;$x<$count;$x++)
								{
?>
									<div style="float: left;width:<?php echo 100/$count; ?>%">
<?php
									$b = $a + $cnt-1;
									for($c=$a;$c<=$b;$c++)
									{
?>										<ul class="ul-hoz">
											<li class="title"><?php echo $c; ?>. </li>
											<li>
<?php 										$dimension = explode(',',$ssv['z_questions'][$x]['length']);
											for($d1=0;$d1<$dimension[0];$d1++)
											{
		?>
											<ul class="ul-hoz">
												<li class="title">(<?php echo $r_char[$d1]; ?>)</li>
		<?php 									for($d2=0; $d2<$dimension[1]; $d2++)
												{
		?>
													<?php echo ($d2==0) ? '' : '<li class="spacing"> &nbsp;</li>'; ?>
													<li class="circle"><img class="img-responsive" src="<?php echo base_url('img/'.$c_char[$d2].'.jpg'); ?>"></li>
													<!-- <li class="circle"><?php echo $c_char[$d2]; ?></li> -->
		<?php 									}
		?>
											</ul>											
		<?php								}
		?>									</li>
										</ul>
<?php								}
									$a = $a + $cnt;
?>									</div>
<?php							}
								break;
							default:
?>
<?php							break;
						}
?>						</div>
<?php 				}
?>				</div>
<?php 			$i++;
			}			
?>			</div>
		</div>
<?php 	$z++;
		if($z==1){ break; }
		}
?>
</body>
</html>









<?php //for ($i=0; $i < 50; $i++) { ?>
		<!-- <ul class="ul-hoz">
			<li class="circle">1</li>
			<li class="spacing"> &nbsp;</li>
			<li class="circle">2</li>
			<li class="spacing"> &nbsp;</li>
			<li class="circle">3</li>
			<li class="spacing"> &nbsp;</li>
			<li class="circle">4</li>
			<li class="spacing"> &nbsp;</li>
			<li class="circle">5</li>
			<li class="spacing"> &nbsp;</li>
		</ul> -->
	<?php //} ?>