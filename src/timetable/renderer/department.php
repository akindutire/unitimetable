<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\View;
	use \zil\factory\Redirect;
	
	use src\timetable\config\config;
	use src\timetable\model\datamanagementmodel as dmgt;

	class department{


		public function __construct(){


		}

		public function index(){

			$cfg = new config;
			$d = new dmgt;

			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $d->getAlldepartment1()];

			View::render('department.php',$Outputdata);

		}
				
	}

?>