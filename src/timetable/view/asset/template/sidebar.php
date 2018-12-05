<?php
use \zil\factory\View;
$link = View::getInfo()['ROUTER_LINK'];

?>
<section class="w3-col l2 m3 s12 w3-border-right w3-border-gray w3-static" style="min-height: 500px; height: auto;">
		
		<ul class="w3-bar-block" style="padding: 0px !important;">
			
			<a href="<?php echo "{$link}faculty"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Faculty</li></a>
			<a href="<?php echo "{$link}department"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Department</li></a>
			<a href="<?php echo "{$link}course"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Course</li></a>


			<a href="<?php echo "{$link}course/import"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Import Course Capacity</li></a>

			<a href="<?php echo "{$link}venue"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Venue</li></a>
			<a href="<?php echo "{$link}timetable"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Generate timetable</li></a>
            
            <a href="<?php echo "{$link}settings"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Settings</li></a>

            

            <a href="<?php echo "{$link}logout"; ?>" style="text-decoration: none;"><li class="w3-padding w3-bar-item w3-hover-gray">Exit</li></a>
		</ul>

	</section>