<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\View;
	use src\timetable\config\config;

	class home{

		
		public function __construct(){

		}

		public function index(){

			$cfg = new config;
			$Outputdata = [$cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(),$cfg->getAppInit()];

			View::render('index.php',$Outputdata);

		}
				
	}

?>