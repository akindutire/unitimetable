<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];

$absPathForLinks = $data[2];

$faculty = $data[3];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">

<div class="w3-modal" id="dept_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a id='Departmenttitle' class="w3-display-topleft w3-padding w3-large"></a>
			

            <span class="w3-display-topright">
                <a onclick="document.getElementById('add_dept_modal').style.display='block';" class=" w3-tag w3-blue-grey w3-padding" style="top:5% !important;"><i class="fa fa-plus"></i> Add Dept.</a>
			    <a onclick="document.getElementById('dept_modal').style.display='none';" class="w3-tag w3-padding w3-red" ><i class="fa fa-times w3-large"></i> </a>
            </span>
        </div>

		<hr class="w3-col">

		<div class="w3-container" style="padding-top: 10% !important;">
			
			<ul class="w3-ul w3-col l12 m12 s12" id="dept_wrap"> 
				
			</ul>

		</div>
	</div>
</div>

<div class="w3-modal" id="add_dept_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a class="w3-display-topleft w3-padding w3-large">Add Department</a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('add_dept_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		<hr class="w3-col">

		<div class="w3-container" style="padding-top: 8% !important;">
			
			<p></p>
			<form class="w3-col l9 m9 s12 w3-form">

				<label>Department Name</label><br>
				<input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="dept"><br><br>

                <label>Department Short Code</label><br>
                <input type="text" maxlength="3" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="dept_short_code"><br><br>

                <label>Lecture Range</label><br>
				<select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="time">
					<option value="1">Early hours</option>
					<option value="2">All hours</option>
				</select><br><br>

                <p class="w3-center w3-margin-top"><button ng-click=add_department($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button></p>

			</form>

		</div>
	</div>
</div>

<div class="w3-modal" id="add_fac_modal" style="display: none;">
	<div class="w3-modal-content w3-white" style="width: 50% !important;">
		
		<div class="w3-display-container" style="top: 5%;">
			<a class="w3-display-topleft w3-padding w3-large">Add Faculty</a>
			
			<a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('add_fac_modal').style.display='none';"><i class="fa fa-times"></i></a>
		</div>

		<hr class="w3-col">

		<div class="w3-container" style="padding-top: 8% !important;">
			
			<p></p>
			<form class="w3-col l9 m9 s12 w3-form">

				<label>Faculty Name</label><br>
				<input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="fac"><br><br>
				<p class="w3-center w3-margin-top"><button ng-click=add_faculty($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button></p>

			</form>

		</div>
	</div>
</div>

<div class="w3-modal" id="edit_fac_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 50% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large">Edit Faculty</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('edit_fac_modal').style.display='none';"><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">

            <p></p>
            <form class="w3-col l9 m9 s12 w3-form">

                <label>Faculty Name</label><br>
                <input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" id="edited_fac_name"><br><br>
                <p class="w3-center w3-margin-top"><button data-sig=1 ng-click=edit_faculty($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Change</button></p>

            </form>

        </div>
    </div>
</div>

<div class="w3-modal" id="edit_dept_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 50% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large">Edit Department</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('edit_dept_modal').style.display='none';"><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">

            <p></p>
            <form class="w3-col l9 m9 s12 w3-form">

                <label>Department</label><br>
                <input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" id="edited_dept_name"><br><br>
                <p class="w3-center w3-margin-top"><button data-sig=1 ng-click=edit_department($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Change</button></p>

            </form>

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
		<p class="w3-col l12 m12 s12 w3-bar w3-animate-zoom">
			<span class="w3-bar-item w3-padding">
				<button onclick="document.getElementById('add_fac_modal').style.display='block';" class="w3-btn w3-blue-grey" type="button"><i class="fa fa-plus"></i>&nbsp;Add</button>
			</span>

			<span class="w3-bar-item w3-padding">
				<button data-sig=1 class="w3-btn w3-red" type="button" ng-click=delete_faculty($event) disabled="disabled" id="delete_faculty_btn"><i class="fa fa-trash"></i>&nbsp;Delete</button>
			</span>

		</p>

		<div class="w3-col l12 m12 s12 w3-padding">

			<ul class="w3-ul w3-bar-block w3-col l8 m8 s12" id="fac_wrap">
				
				<?php

					if(count($faculty) == 0){
						goto nofac;
					}

					foreach ($faculty as $fac_id => $name) {
						echo "<li class='w3-bar-item w3-border-bottom w3-padding'>
								<a data-fac-id={$fac_id} data-sig=1 ng-click=open_edit_faculty(\$event) class='w3-col l1 m1 s2' style='cursor:pointer !important;'><i class='fa fa-pencil w3-text-green'></i></a>
								
    							<input type='checkbox' class='w3-col l1 m1 s2 w3-padding-right' name='faculty_checkbox' id='faculty_checkbox' data-fac-id='{$fac_id}' ng-click=select_faculty(\$event)>&nbsp;

								<a data-fac-id={$fac_id} ng-click=open_my_department(\$event) class='w3-col l9 m9 s7' style='cursor:pointer !important;'>{$name}</a>

							</li>";
					}

					goto nop;

					nofac:
						echo "<p class='w3-xxlarge' id='nodata'>No faculty found</p>";

					nop:
				?>

			</ul>
			

		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>


<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/timetable.js"; ?>"></script>

</html>
