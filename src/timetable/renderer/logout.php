<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\Redirect;
	
	use src\timetable\config\config;

	
 
	class logout{

		
		public function __construct(){

		}

		public function index(){


			$cfg = new config;
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $cfg->getAppInit()];
			
			
			session_unset($_SESSION['App_Cert']);
			session_unset($_SESSION['App_Cert_Id']);			
	
			new Redirect("{$Outputdata[3]}");


		}
				
	}

?>