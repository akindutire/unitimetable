<?php
	
	namespace src\timetable\renderer;

	use \zil\factory\View;
	use \zil\factory\Session;
	use \zil\factory\Sanitize;
	use \zil\factory\Redirect;
	
	use src\timetable\config\config;
	use \src\timetable\model\datamanagementmodel as dmgt;

	class venue{

		
		public function __construct(){

		}

		public function index(){

			$cfg = new config;
			$d = new dmgt;
			$v_arr = $d->getAllvenue();
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $v_arr['f_arr_venue'], 'venue_op_disabled' => $v_arr['venue_op_disabled'], 'venue_in_use' => $v_arr['v_in_use']];
			
			unset($v_arr);
			View::render('venue/venue.php',$Outputdata);


		}

		public function import(){

			$cfg = new config;
			$d = new dmgt;
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl()];

			View::render('venue/venue_import.php',$Outputdata);

		}

				
	}

?>