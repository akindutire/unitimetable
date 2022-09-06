<?php
use \zil\factory\View;
$link = View::getInfo()['ROUTER_LINK'];

?>
<section class="w3-col l2 m3 s12 w3-border-right w3-border-gray w3-static" style="min-height: 500px; height: auto;">
		
		<ul class="w3-bar-block robo-side" style="padding: 0px !important;">
			
			<a href="<?php echo "{$link}faculty"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Faculty</li></a>
			<a href="<?php echo "{$link}department"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Department</li></a>
			
			<div class="w3-dropdown-hover">
			
				<a href="<?php echo "{$link}course"; ?>" style="text-decoration: none; width: 80% !important;"><li class="w3-padding w3-bar-item w3-hover-gray">Course </li></a>
				<!-- <span style="width: 10%;" class="w3-dropdown-click w3-right"><i class="fas fa-chevron-down"></i></span> -->
			
					<div class="w3-dropdown-content w3-bar-block">
						<a href="<?php echo "{$link}course/import"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Import Course Capacity</li></a>

						<!-- <a  href="<?php //echo "{$link}course/import"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Course Chart</li></a> -->
					</div>

			</div>
			
			
			<a href="<?php echo "{$link}venue"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Venue</li></a>
			
			<a href="<?php echo "{$link}timetable"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Allocation</li></a>
            
            <a href="<?php echo "{$link}settings"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Settings</li></a>

            

            <a href="<?php echo "{$link}logout"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item">Exit</li></a>
		</ul>

	</section>

	<style>
		ul.robo-side a li{
			margin-bottom: 1rem;
			border-left: 0.7rem solid #303f9f !important;
		}

		ul.robo-side a li:hover{
			background:  rgba(48, 63, 159, 0.8) !important;
			color: #fff !important;
		}

		ul.robo-side a:active li{
			background: rgba(48, 63, 159, 0.8) !important;
			color: #fff !important;
		}

		/* ul.robo-side a:active li{
			border-left: 2px solid rgba(30, 3f, 9f, 1);
		} */
	</style>