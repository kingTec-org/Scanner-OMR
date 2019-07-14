<!DOCTYPE html>
<html>
<head>
	<!-- <title>OMR ANSWER SHEET PDF</title> -->
	<link rel="stylesheet" type="text/css" href="<?php echo base_url('css/pdf.css'); ?>">
</head>
<body>

<?php 
		$subjects = explode(',',$data['exam']['subjects']);
		$total_subjects = count($subjects);
		foreach($data['students'] as $students){ 
?>
		<div style="height:100% !important;">
			<div style="height:15% !important;border:1px solid black;margin-bottom: 10px;padding-right:10px;padding-left: 10px;"><pre><?php print_r($students); ?></pre></div>
			<div style="height:84% !important;width:99.7%;border:1px solid black;">
<?php 		foreach ($subjects as $ks=>$subject) {
				$ky = array();
				for($i = 0;$i<count($data['exam']['z_sections']);$i++)				
				{
					if($i%$total_subjects == $ks)
					{
						$ky[] = $i;
					}
				}
?>
				<div style="text-align:center;float:left;height:100% !important;<?php echo ($ks==0) ? '':'border-left:1px solid black;'; ?>width: <?php echo 99.7/$total_subjects; ?>%">
<?php 				foreach($ky as $key) {
?>
					<div style="border-bottom: 1px solid black">
						<div class="section_title"><?php echo $data['exam']['z_sections'][$key]['section']; ?>
						</div>
<?php 					$f = 0;
		// My Mistake 	$sec_div_count = (count($data['exam']['z_sections'])/$total_subjects);
						$sec_div_count = 6/$total_subjects;
						for($k = 0;$k<$sec_div_count;$k++)
						{
?>							
							<div style="float:left;width:<?php echo 98.2/$sec_div_count; ?>%;<?php echo ($k==0) ? '':'border-left:0px solid black;'; ?>">
							<!-- <br> -->
<?php 
							$g = ceil($f);
							$f = ceil($f + ($data['exam']['z_sections'][$key]['no_of_qn']/$sec_div_count));
							for($j = $g;$j<$f;$j++)	
							{
?>
								<ul class="ul-hoz">
<?php 							if(array_key_exists((int)$j,$data['exam']['z_sections'][$key]['z_questions']) && ($data['exam']['z_sections'][$key]['z_questions'][$j]['qn_type'] == 'single' || $data['exam']['z_sections'][$key]['z_questions'][$j]['qn_type'] == 'multiple'))
								{
?>
									<li class="title"><?php echo $j+1; ?>. </li>
<?php								for ($c=0; $c < $data['exam']['z_sections'][$key]['z_questions'][$j]['length']; $c++){ 
?>
									<li class="spacing"> &nbsp;</li>
									<li class="circle"><?php echo $c+1; ?></li>
<?php 								}
								}
								else if(array_key_exists((int)$j,$data['exam']['z_sections'][$key]['z_questions']) && $data['exam']['z_sections'][$key]['z_questions'][$j]['qn_type'] == 'matrix')
								{
?>
									<li class="title"><?php echo $j+1; ?>. </li>
									<li>
<?php 								$dimension = explode(',',$data['exam']['z_sections'][$key]['z_questions'][$j]['length']);
									for($l=0;$l<$dimension[0];$l++)
									{
?>
									<ul class="ul-hoz">
<?php 									for($b=0; $b<$dimension[1]; $b++)
										{
?>
											<li class="circle"><?php echo $b+1; ?></li>
											<li class="spacing"> &nbsp;</li>
<?php 									}
?>
									</ul>											
<?php								}
?>									</li>
									<!-- <br> -->
<?php							}
								else if(array_key_exists((int)$j,$data['exam']['z_sections'][$key]['z_questions']) && $data['exam']['z_sections'][$key]['z_questions'][$j]['qn_type'] == 'numeric')
								{
?>
									<li class="title"><?php echo $j+1; ?>. </li>
									<li>
<?php								for ($n=0;$n<=9;$n++) {
?>
									<ul class="ul-hoz">
<?php									for($c=0; $c < $data['exam']['z_sections'][$key]['z_questions'][$j]['length']; $c++)
										{
?>
											<li class="circle"><?php echo $n; ?></li>
											<li class="spacing"> &nbsp;</li>
<?php									}
?>
									</ul>
<?php								}
?>
									</li>
<?php						
								}
								else{
?>
									<li class="title">&nbsp; </li>
									<li class="spacing"> &nbsp;</li>
<?php
								}
?>								</ul>
<?php						}
?>
							</div>
<?php 						}
?>					</div>
<?php				}
?>
				</div>	
<?php 		}			
?>			</div>
		</div>
<?php 	break;}
?>
</body>
</html>









<?php for ($i=0; $i < 50; $i++) { ?>
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
	<?php } ?>