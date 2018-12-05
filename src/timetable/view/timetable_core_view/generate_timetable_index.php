<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];
$absPathForLinks = $data[2];
$baselink = $data[3];
$tolerance = $data[4];

$update_timetable_disabled = true;
if($data['semester_on_allocation'] !== false){
	$update_timetable_disabled = false;
}

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>

<script>
	function busy(element){
		element.setAttribute("disabled", "disabled");
		element.innerHTML = 'In Progress...';

		document.querySelector('form#allocationfrm').submit();
	}
</script>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; ">



<!--An header-->
	<?php  include_once("{$absPath}view/asset/template/header.php"); ?>
<!--End header-->

<article class="w3-col l12 m12 s12 w3-padding" style="margin-top: 6% !important; padding-left: 0px !important;">
	
	<!--Sidebar here-->
		<?php include_once("{$absPath}view/asset/template/sidebar.php"); ?>
	

	<section class="w3-col l10 m9 s12">
		
		<div class="w3-col l12 m12 s12 w3-animate-zoom w3-padding w3-margin-top">

			<span class="w3-col l2 m3 s12 w3-padding w3-card-2 w3-border w3-round w3-margin-right">
				<a class="w3-col l12 m12 s12  w3-btn w3-round w3-border-blue-gray w3-blue-grey w3-padding" style="" href="<?php echo "{$data['ROUTER_LINK']}day"; ?>">View Timetable</a>
			</span>
			
			<span class="w3-col l2 m3 s12 w3-padding w3-card-2 w3-border w3-round w3-margin-right">
				<a class="w3-col l12 m12 s12  w3-btn w3-round w3-border-red w3-red w3-padding" style="" href="<?php echo "{$data['ROUTER_LINK']}timetable/reset"; ?>">Reset Timetable</a>
			</span>


			 <form class="w3-col l3 m5 s12 w3-border w3-card-2 w3-round w3-margin-right" style="" action="<?php echo "{$baselink}timetable/tolerance"; ?>" method="post">

                <span class="w3-col l4 m5 s4 w3-padding" style="padding-right: 2px !important;">

                    <input title="Select Tolerance" type="number" min="5" max="50" name="tolerance" value="<?php echo $tolerance; ?>" id="select_sem" class="w3-input w3-border w3-round"> 

                </span>
                <span class="w3-col l1 m1 s2 w3-padding-right w3-margin-top w3-large">%</span>
                <span class="w3-col l3 m3 s5 w3-padding" style="padding-right: 0px !important;">
                    <button class="w3-btn w3-blue-grey w3-round">Lose Tolerance</button>
                </span>

            </form>

			
		</div>


		<div class="w3-col l12 m12 s12 w3-padding-large" style="display: flex; justify-content: center; align-items: center; height: 400px;">
			
		   <form class="w3-col l3 m6 s12" id="allocationfrm" style="margin-top: 1%;" action="<?php echo "{$baselink}timetable/generate"; ?>" method="post">

                    <select title="Select Semester" name="sem" id="select_sem" class="w3-select w3-border w3-round">
						<?php

							if($update_timetable_disabled){
								echo "<option value='0' class='w3-bar-item w3-padding w3-border-bottom'>--Select a semester--</option>
								<option value='harmathan' class='w3-bar-item w3-padding w3-border-bottom'>Harmattan</option>
								<option value='rain' class='w3-bar-item w3-padding w3-border-bottom'>Rain</option>";
							}else{
								if($data['semester_on_allocation'] == 1)
									echo "<option value='harmathan' class='w3-bar-item w3-padding w3-border-bottom'>Harmattan</option>";
								elseif($data['semester_on_allocation'] == 2)
									echo "<option value='rain' class='w3-bar-item w3-padding w3-border-bottom'>Rain</option>";
							}
						?>
                        
                    </select>
					<div class="w3-col l12 m12 s12" style="position: relative;">

						<?php if($update_timetable_disabled){ 
							echo "<p class='w3-center w3-padding'>
								<button class='w3-btn w3-green w3-round' onclick='busy(this)'>Generate</button>
							</p>";
						}else{
							echo "<p class='w3-center w3-padding'>
								<button class='w3-btn w3-orange w3-round' onclick='busy(this)'>Update Fixed Allocation</button>
							</p>";
						}
						?>
					</div>
                
            </form>

		</div>
	</section>

</article>

<!--Footer-->
	<?php include_once("{$absPath}view/asset/template/footer.php"); ?>


</body>

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

</html>
