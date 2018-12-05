<?php
	
	namespace src\timetable\renderer;
	

	use \zil\factory\View;

	use \zil\factory\Redirect;
	
	
	use src\timetable\config\config;

	use src\timetable\model\datamanagementmodel as dmgt;

	class course{

		

		public function __construct(){

			$cfg = new config;

		}

		public function index(){

			$cfg = new config;

            $d = new dmgt;
            
			$Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $d->getRandomCourse(50), $cfg->getAppInit(),  $d->getAlldepartment(), $d->getAllvenue()['f_arr_venue'] ];

			View::render('timetable_course_view/v_random.php',$Outputdata);

		}

		public function s(){

            $cfg = new config;

            $d = new dmgt;

            $Outputdata = [ $cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), null, $cfg->getAppInit(), $d->getAlldepartment(),null,null];

            if(isset($_POST['sem'])){

                $semesterIndex = trim(strtolower($_POST['sem']));

                $Outputdata[3] = $d->getAllCourseBySemester($semesterIndex);
                
                $Outputdata[6] = ucwords($semesterIndex);
                
                $Outputdata[7] = $d->getAllvenue()['f_arr_venue'];

                View::render('timetable_course_view/v_coursebysemester.php',$Outputdata);

            }else{

		        new Redirect("{$Outputdata[4]}course");
            }

        }


		public function c(){

			$cfg = new config;
			$d = new dmgt;

			if(isset($_POST['dept']) ){
				
				$Outputdata = [ $cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $d->get_course($_POST['dept']), $cfg->getAppInit(), $d->getAllvenue()['f_arr_venue'], $d->getAlldepartment() ];

                View::render('timetable_course_view/v_coursebydept.php',$Outputdata);
			
			}else{
				
				new Redirect("{$cfg->getAppInit()}course");
			}
		}
		

		public function import(){

			$cfg = new config;
			$d = new dmgt;
			$Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl()];

			View::render('timetable_course_view/course_participant_import.php',$Outputdata);

		}

		public function sandbox(){
			
			$cfg = new config;
			$d = new dmgt;
			//$d->sandbox();
		}
	

	}

?>