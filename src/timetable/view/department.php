<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];

$absPathForLinks = $data[2];
$alldept = $data[3];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">

<div class="w3-modal" id="add_option_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a id="Departmenttitle" class="w3-display-topleft w3-padding w3-large"></a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('add_option_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		<hr class="w3-col">

		<div class="w3-container" style="padding-top: 8% !important;">
			<ul class="w3-ul w3-bar-block w3-col l7 m6 s12" style="padding-bottom: 64px !important;" id="option_wrap">
				
			</ul><br><br>


			
			<p class="w3-col l12 m12 s12" id="error"></p>
			<form class="w3-col l9 m9 s12 w3-form">

				<label>Options</label><br>
				<input type="text" required="required" class="w3-input w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="option"><br><br>
				<label>Lecture Range</label><br>
				<select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="time">
					<option value="1">Early hours</option>
					<option value="2">All hours</option>
				</select><br><br>
				
				<p class="w3-center"><button ng-click=add_department_option($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round w3-margin-left">Add</button></p>

			</form>
			<br>

			
		</div>
	</div>
</div>


<!--An header-->
	<?php  include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 6% !important; padding-left: 0px !important;">
	
	<!--Sidebar here-->
		<?php include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l10 m9 s12">
		<!--<p class="w3-col l12 m12 s12 w3-bar w3-animate-zoom">
			<span class="w3-col l5 m6 s12 w3-padding">
				<input type="text" class="w3-input w3-col l10 m8 s7 w3-border" ng-model="searchdept" placeholder="Search Department"><button class="w3-col l2 m4 s5  w3-button w3-border w3-border-blue-gray w3-blue-grey w3-padding-large" style="" type="button"><i class="fa fa-search"></i></button>
			</span>
			
		</p>-->

		<div class="w3-col l12 m12 s12 w3-padding">

			<?php

				if(count($alldept) == 0)
					goto nodata;

				foreach ($alldept as $faculty_name => $dept_arr) {
					
					echo "<p class='w3-col l12 m12 s12 w3-large'>{$faculty_name}</p>";
					echo "<ul class='w3-ul w3-bar-block w3-col l4 m6 s12'>";
						
						foreach ($dept_arr as $dkey => $data) {
							echo "<li data-dept-id={$dkey} ng-click=open_my_option(\$event) class='w3-bar-item w3-padding w3-border-bottom'>{$data[0]} ($data[1]) </li>";
						}
					echo "</ul>";
				}
			
				goto nop;

				nodata:echo "<p class='w3-large'>No data found</p>";

				nop:
			?>

				


		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/timetable.js"; ?>"></script>

</html>
