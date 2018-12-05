<?php
	use \zil\factory\View;
	$data = View::getInfo();
	
	$absPath = $data[0];
	$absPathForLinks = $data[2];

	$forgotten_courses = $data[3];
	
	$baselink = $data[4];

	

	$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>



</head>


<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; forgotten='<?php echo sizeof($forgotten_courses); ?>' " style="margin-top: 90px;">

<header class="w3-xlarge w3-top" style="z-index: 1; background: rgba(0,0,0, .8); color: white; height: 70px;">
	
	<p class="w3-center" style="margin-top: 10px;">{{ forgotten }} Forgotten Allocation </p>

</header>




<!--An header-->
	<?php  //include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->
<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 0px !important; padding-left: 32px !important; padding-right: 32px !important;">
	
	<!--Sidebar here-->
		<?php //include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l12 m12 s12">
		
		

		<table class="w3-table w3-col l12 m12 s12 w3-small w3-border w3-bordered w3-center">
			<thead>
				<tr>
					
					<td>Course</td>
					<td></td>
					<td class="w3-center">Capacity</td>
					<td class="w3-center">Allocated</td>
					<td class="w3-center">Expected Allocation</td>
					<td class="w3-center">Undo</td>
					
					
				</tr>
			</thead>
			<tbody class="w3-bordered w3-border-right">
				<?php
					$i = 0;
					foreach ($forgotten_courses as $code => $params) {
						
						echo "<tr>
								
								<td>{$code}</td>
								<td>{$params[0]}</td>
								<td class='w3-center w3-border'>{$params[4]}</td>
								<td class='w3-center w3-border'>{$params[3]}</td>
								<td class='w3-center w3-border'>{$params[2]}</td>
								<td class='w3-center'><a class='w3-btn w3-round w3-red' ng-click=undo_override_course(\$event) data-code='{$code}'>Undo</td>
							</tr>";
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


</body>


</html>
