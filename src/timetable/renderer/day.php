<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\View;
	use \zil\factory\Redirect;

	use src\timetable\config\config;
	use src\timetable\model\timetablemodel;

	class day{

		
		public function __construct(){

			
		}

		public function index(){

			$cfg = new config;

			$Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $cfg->getAppInit()];

			View::render('timetable_by_day_view/day.php',$Outputdata);


		}

		public function day(){

			$cfg = new config;

			$Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $cfg->getAppInit()];

			View::render('timetable_by_day_view/day.php',$Outputdata);

		}

        public function reset($param){

        	 $cfg = new config;

            $timetable = new timetablemodel();

            $Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $cfg->getAppInit()];

			$day_id = $param[0];

            if($timetable->resetADay($day_id) === true){

	            
	            new Redirect("{$Outputdata[3]}day");

        	}else{

        		new Redirect("{$Outputdata[3]}day");
        	
        	}

        }


        public function open($param){

        	$cfg = new config;

            $timetable = new timetablemodel();

			$day_id = $param[0];

            $Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $timetable->open($day_id), $day_id, $cfg->getAppInit()];

            View::render('timetable_by_day_view/tview.php',$Outputdata);
        }


        public function unallocated(){

        	$cfg = new config;

            $timetable = new timetablemodel();


            $Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $timetable->open_unallocated(), $cfg->getAppInit()];

            View::render('timetable_by_day_view/tview_unallocated.php',$Outputdata);
		}
		
		public function forgotten(){

        	$cfg = new config;

            $timetable = new timetablemodel();


            $Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $timetable->open_forgotten(), $cfg->getAppInit()];

            View::render('timetable_by_day_view/tview_forgotten.php',$Outputdata);
        }
				
	}

?>