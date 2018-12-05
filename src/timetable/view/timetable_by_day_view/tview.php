<?php
	use \zil\factory\View;
	$data = View::getInfo();
	
	$absPath = $data[0];
	$absPathForLinks = $data[2];
	$today_timetable = $data[3];
	
	$baselink = $data[5];

	$cur_day = $data[4];

	$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">



<!--An header-->
	<?php  //include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 0px !important; padding-left: 32px !important; padding-right: 32px !important;">
	
	<!--Sidebar here-->
		<?php //include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l12 m12 s12">
		
	
		
		<p class="w3-col w3-center w3-xxlarge"><?php echo $days[$cur_day-1]; ?></p>

		<table class="w3-table w3-col l12 m12 s12 w3-small w3-border w3-bordered" style="margin-top: 16px !important;">
			<thead>
				<tr>
					<th>Venue</th>
					<th>Location</th>
					<th>8/9am</th>
					<th>9/10am</th>
					<th>10/11am</th>
					<th>11/12pm</th>
					<th>12/1pm</th>
					<th>1/2pm</th>
					<th>2/3pm</th>
					<th>3/4pm</th>
					<th>4/5pm</th>
					<th>5/6pm</th>
					
				</tr>
			</thead>
			<tbody class="w3-bordered w3-border-right">
				<?php

					foreach ($today_timetable as $venue_name => $v_param) {
						
						echo "<tr><td class='w3-border-right'>{$venue_name}</td>";
						echo "<td class='w3-border-right'>{$v_param['location']}</td>";	
						

						ksort($v_param['course']);
						

						$nextcount = 0;
						foreach ($v_param['course'] as $id => $course) {
							

							for($i = $nextcount; $i <= 9; $i++){							

								if($id == $i){

									echo "<td class='w3-border-right'>{$course}</td>";
									$nextcount = $id+1;
									break;
								
								}else{
									
									echo "<td class='w3-border-right'></td>";
									continue;	
								
								}
							}
							
									
						}

						echo "</tr>";
					
					}


				?>
			</tbody>
		</table>

	</section>

</article>

<!--Footer-->
	<?php //include_once("{$absPath}view/asset/template/footer.php"); ?>



<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

<script src="<?php echo "{$absPathForLinks}view/asset/bower_components/jquery/dist/jquery.min.js"; ?>"></script>
<script src="<?php echo "{$absPathForLinks}view/asset/bower_components/js-xlsx/dist/xlsx.core.min.js"; ?>"></script>
<script src="<?php echo "{$absPathForLinks}view/asset/bower_components/file-saverjs/FileSaver.min.js"; ?>"></script>
<script src="<?php echo "{$absPathForLinks}view/asset/bower_components/tableexport.js/dist/js/tableexport.min.js"; ?>"></script>

<script>
	$("table").tableExport({
    	bootstrap: false
	});

	function ExportTo(type){
		
		$("table").tableExport();
	}
</script>






</body>

</html>
