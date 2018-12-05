<?php
use \zil\factory\View;
$data = View::getInfo();

$absPath = $data[0];

$absPathForLinks = $data[2];
$course= $data[3];

$baselink = $data[4];

$venue = $data[5];

$alldept = $data[6];

?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>timetable</title>
</head>

<body ng-app="app" ng-controller="ctrl" ng-init=" absPath='<?php echo $absPathForLinks; ?>'; baselink='<?php echo $baselink; ?>'; dept_code='<?php echo $course['dept_info'][0]; ?>'; ">



<?php include_once("course_modal_includes.php"); ?>

<div class="w3-modal" id="add_course_modal" style="display: none;">
    <div class="w3-modal-content w3-white" style="width: 50% !important;">

        <div class="w3-display-container" style="top: 5%;">
            <a class="w3-display-topleft w3-padding w3-large">Add Course</a>

            <a class="w3-display-topright w3-tag w3-red w3-padding" onclick="document.getElementById('add_course_modal').style.display='none';"><i class="fa fa-times"></i></a>
        </div>

        <hr class="w3-col">

        <div class="w3-container" style="padding-top: 8% !important;">

            <p></p>
            <form class="w3-col l9 m9 s12 w3-form">

                <input type="checkbox" class="w3-input" style="width: auto; margin-right: 8px; display: inline;" id="c_prac">Is Practical?<br><br>

                <label>Department</label><br>
                <select title="Select Department" name="dept" id="select_dept" ng-model="c_dept" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom">

                    <?php

                        echo "<option value={$course['dept_info'][0]} class='w3-bar-item w3-padding w3-border-bottom'  selected>{$course['dept_info'][1]}</option>";
                   
                    ?>
                </select><br>

                <label>Title</label><br>
                <input type="text" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="c_title"><br><br>

                <label>Code</label>
                <input type="text" maxlength="6" class="w3-input w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="c_code"><br><br>

                <label>Unit</label><br>
                <select class="w3-select w3-required w3-col l12 m12 s12 w3-border w3-round w3-margin-bottom" ng-model="c_unit">
                    <option value="0">0</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                </select><br><br>

                

                <p class="w3-center w3-margin-top"><button ng-click=add_course($event) type="button" class="w3-btn w3-blue-grey w3-padding w3-ripple w3-round">Save</button></p>

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
		<section class="w3-col l12 m12 s12 w3-animate-zoom w3-padding-large">

            <form class="w3-col l4 m12 s12 w3-border w3-card-2 w3-round w3-margin-right" style="margin-top: 1%;" action="<?php echo "{$baselink}course/s"; ?>" method="post">

                <span class="w3-col l9 m9 s9 w3-padding">

                    <select title="Select Semester" name="sem" id="select_sem" class="w3-select w3-border w3-round">

                        <option value='' class='w3-bar-item w3-padding w3-border-bottom'>--Select Semester--</option>
                        <option value='harmathan' class='w3-bar-item w3-padding w3-border-bottom'>Harmattan</option>
                        <option value='rain' class='w3-bar-item w3-padding w3-border-bottom'>Rain</option>

                    </select>
                </span>
                <span class="w3-col l2 m2 s3 w3-padding" style="padding-right: 0px !important;">
                    <button class="w3-btn w3-green w3-round">View</button>
                </span>

            </form>


            <form class="w3-col l4 m12 s12 w3-border w3-card-2 w3-round w3-margin-right" style="margin-top: 1%;" action="<?php echo "{$baselink}course/c"; ?>" method="post">

                <span class="w3-col l9 m9 s9 w3-padding">

                   <select title="Select Department" name="dept" id="select_dept" class="w3-select w3-border w3-round">

                        <option value='' class='w3-bar-item w3-padding w3-border-bottom'>--Select Department--</option>
                        <?php

                        foreach ($alldept as $faculty_name => $dept_arr) {

                            foreach ($dept_arr as $dkey => $data) {
                                echo "<option value={$data[1]} class='w3-bar-item w3-padding w3-border-bottom'>{$data[0]}</option>";
                            }
                        }

                        ?>
                    </select>

                </span>
                <span class="w3-col l2 m2 s3 w3-padding" style="padding-right: 0px !important;">
                    <button class="w3-btn w3-green w3-round">View</button>
                </span>

            </form>

            <div class="w3-col l2 m12 s12 w3-padding w3-border w3-round w3-card-2" style="margin-top: 1%;">

                <button onclick="document.getElementById('add_course_modal').style.display='block';" class="w3-btn w3-green w3-round">Add Course</button>

            </div>



        </section>


		<div class="w3-col l12 m12 s12 w3-padding">

			<?php

				$dept_h = ucwords($course['dept_info'][1]);
				
				echo "<p class='w3-xlarge w3-col l12 m12 s12'>{$dept_h}</p>";

				if(count($course) == 0)
					goto nodata;


				foreach ($course as $semester => $level) {
								
					if($semester != 'dept_info'){

						echo "<p class='w3-large w3-col l12 m12 s12'>{$semester}</p>";

							echo "<table class='w3-table w3-col l12 m12 s12 w3-small'>
								<thead class='w3-light-gray'>
									<tr>
										<th>#</th><th>Title</th><th>Code</th><th>Unit</th> <th>Level</th> <th>Total Hours Offered</th> <th>Straight Hours</th> <th>Class</th> 
									</tr>
								</thaed>
								<tbody>
							";
							foreach ($level as $l => $course_arr) {
										
								foreach ($course_arr as $code => $course_details) {
									
									echo "<tr class='w3-border-bottom'>
										<td><a class='w3-tag w3-white' data-sig=1 data-c-code='{$code}' ng-click=open_course_setting(\$event)><i class='fa fa-cog'></i></a></td> 
										<td>{$course_details[0]}</td> 
										<td>{$code}</td> 
										<td>{$course_details[1]}</td> 
										<td>{$l}</td>
										<td>{$course_details[2]}</td> 
										<td>{$course_details[3]}</td>  
										<td>{$course_details[4]}</td>
										
										</tr>";	
								}
							}

							echo "</tbody>
								</table><br><br>
							";
				
					}
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

<script type="text/javascript" src="<?php echo "{$absPathForLinks}view/asset/js/angularApp/course.js"; ?>"></script>

</html>
