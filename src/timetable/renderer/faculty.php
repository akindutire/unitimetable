<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\Logger;
	use \zil\factory\Session;
	use \zil\factory\View;
	
	use \zil\factory\Redirect;
	
	use src\timetable\config\config;
	use src\timetable\model\datamanagementmodel as dmgt;

	class faculty{

		
		public function __construct(){

		}

		public function index(){

			$cfg = new config;

			$d = new dmgt;

			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $d->getfaculty()];

			View::render('faculty.php',$Outputdata);

		}
				
	}

?>