<?php
	use \zil\factory\View;
	$data = View::getInfo();
	
	$absPath = $data[0];
	$absPathForLinks = $data[2];

	$unallocated_courses = $data[3];
	
	$baselink = $data[4];

	

	$days = ['Monday','Tuesday','Wednesday','Thursday','Friday'];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>

<script type="text/javascript">
	
	function fix_course_modal(element){

		code = element.getAttribute('data-code');
		
		document.getElementById('CourseCodeId').innerHTML = code;

		document.getElementById('fix_course_modal').style.display = 'block';

	}	
	
</script>

</head>


<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; unallocated='<?php echo sizeof($unallocated_courses); ?>'; tolerance=20; gwork.models.tolerance=20 " style="margin-top: 90px;" ng-cloak>

<header class="w3-xlarge w3-top" style="z-index: 1; background: rgba(0,0,0, .8); color: white; height: 70px;">
	
	<p class="w3-center" style="margin-top: 10px;">{{ unallocated }} Incomplete Allocation </p>
	
	<a ng-show="gwork.unallocatedCheckListLength > 0" onclick="document.getElementById('batch_fix_course_modal').style.display = 'block';" style="position: absolute; box-shadow:0px 0px 59px -3px rgba(0,0,0,0.75); right: 40px; top: 40px; background: #ff9100; border-radius: 50%; padding: 16px;"> <i class="fa fa-wrench"></i></a>

</header>


<div class="w3-modal" id="fix_course_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important; opacity:0.9;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a id="CourseCodeId" class="w3-display-topleft w3-padding w3-large"></a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('fix_course_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		

		<div class="w3-container" style="padding-top: 3% !important; ">
			
			<hr class="w3-col">
			
			<p class="w3-col l12 m12 s12 w3-padding w3-margin w3-medium" id="error">{{ tolerance_feedback }}</p><br><br>
			<form class="w3-col l9 m9 s12 w3-form">

				<input type="checkbox" class="w3-input" style="width: auto; margin-right: 8px; display: inline;" id="c_clash" checked>Avoid Course Clash<br><br>

				<label>Tolerance</label><br>
				<input type="number" min="0" max="100" required="required" ng-model='tolerance' class="w3-input w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom"><br><br>

				<label>Day</label><br>
				<select ng-model='day' class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom">
					
					<option value="1">Monday</option>
					<option value="2">Tuesday</option>
					<option value="3">Wednesday</option>
					<option value="4">Thursday</option>
					<option value="5">Friday</option>

				</select><br><br>
				
				<p class="w3-center"><button ng-click=fix_course($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round w3-margin-left">Fix</button></p>

			</form>
			<br>

			
		</div>
	</div>
</div>

<div class="w3-modal" id="batch_fix_course_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important; opacity:0.85;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a class="w3-display-topleft w3-padding w3-large">Batch Fix</a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('batch_fix_course_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		

		<div class="w3-container" style="padding-top: 3% !important;">
			
			<hr class="w3-col">
			
			
			<form class="w3-col l12 m12 s12 w3-form">

				<br><input type="checkbox" class="w3-input" style="width: auto; margin-right: 8px; display: inline;" id="bc_clash" checked>Avoid Course Clash<br><br>

				<br>
				
				<div class="w3-col l6 m6 s12 w3-padding">
					<input type="number" min="0" max="100" required="required" ng-model='gwork.models.tolerance' class="w3-input w3-required w3-border w3-round"><small class="w3-center">Tolerance</small>
				</div>
					
				<div class="w3-col l6 m6 s12 w3-padding">
					<select ng-model='gwork.models.day' class="w3-select w3-required w3-border w3-round">
						
						<option value="1">Monday</option>
						<option value="2">Tuesday</option>
						<option value="3">Wednesday</option>
						<option value="4">Thursday</option>
						<option value="5">Friday</option>

					</select>
				</div>
				
				<div class="w3-row" ng-repeat="(c,v) in gwork.unallocatedCheckList">
					
					<div class="w3-col l12 m12 s12 w3-padding">
						<input type="text" required="required" value={{c}} readonly class="w3-input w3-required  w3-border w3-round">
					</div>
						
					

					<br><br>

				</div>

				<p class="w3-center"><button ng-click=batch_fix_course($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round w3-margin-left">Batch Fix</button></p>

			</form>
			<br>

			
		</div>
	</div>
</div>

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
					<td></td>
					<td>#</td>
					<td>Course</td>
					<td></td>
					<td class="w3-center">Capacity</td>
					<td class="w3-center">Allocated</td>
					<td class="w3-center">Expected Allocation</td>
					<td class="w3-center">Fix</td>
					<td class="w3-center">Override</td>
					
					
				</tr>
			</thead>
			<tbody class="w3-bordered w3-border-right">
				<?php
					$i = 0;
					foreach ($unallocated_courses as $code => $params) {
						$i += 1;
						echo "<tr>
								<td>{$i}</td>
								<td><input ng-click='stageUnallocatedChecks(\$event)' type='checkbox' name='' value='{$code}'></td>
								<td>{$code}</td>
								<td>{$params[0]}</td>
								<td class='w3-center w3-border'>{$params[4]}</td>
								<td class='w3-center w3-border'>{$params[3]}</td>
								<td class='w3-center w3-border'>{$params[2]}</td>
								<td class='w3-center'><a class='w3-btn w3-round w3-blue' onclick=fix_course_modal(this) data-code='{$code}'>Fix</td>
								<td class='w3-center'><a class='w3-btn w3-round w3-red' ng-click=override_course(\$event) data-code='{$code}'>Forget</td>
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
