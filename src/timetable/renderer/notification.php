<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\View;

	use src\timetable\config\config;

	
	class notification{

		
		public $AbsPath = null;
		
		
		public function __construct($notifState,$message){

			$cfg = new config;

            $this->index($notifState,$message);
		}

		private function index($notifState,$message){

			$cfg = new config;
            
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), [$message] ];

            if($notifState)
    			View::render('notif/notifSuccess.php',$Outputdata);
            else
                View::render('notif/notifError.php',$Outputdata);
            
		}

	}

?>