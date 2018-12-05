<?php
	
	namespace src\timetable\renderer;
	
	use \zil\factory\View;
	use \zil\factory\Redirect;
    use \zil\factory\Session;

	use src\timetable\config\config;
	use src\timetable\model\timetablemodel;

	class timetable{

		public function __construct(){

            $this->validateMasterUser();

		}

        private function validateMasterUser(){

            if( Session::getSession('App_Cert_Id') != 'master'){
               
               $cfg = new config;

               $Outputdata = [ $cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppInit()];

                $template = "<span class='w3-large'>Access Denied</span><br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}faculty'>Back</a>";
                        
                new notification(false, $template); 

                die();
            }

        }

		public function index(){

			$cfg = new config;

			$timetable = new timetablemodel();

			$Outputdata = [ $cfg->getAppPath(),'view/asset/img/ui1.jpg',$cfg->getAppUrl(), $cfg->getAppInit(), $timetable->getTolerance(), 'semester_on_allocation'=>$timetable->whichSemesterOnAllocation() ];

			View::render('timetable_core_view/generate_timetable_index.php',$Outputdata);


		}

		public function generate(){

            $cfg = new config;

            $timetable = new timetablemodel();

			if(!empty($_POST['sem'])){
				$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $timetable->generatebyWeek($_POST['sem']), $cfg->getAppInit()];

				new Redirect("{$Outputdata[4]}timetable/v");
			}else{
				$Outputdata = [null, null, $cfg->getAppUrl(), null, null];
				$template = "<span class='w3-large'>No Semester Selected</span><br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$cfg->getAppInit()}timetable'>Back</a>";
                        
                new notification(false, $template); 

                die();
			}
        }		

        public function reset(){

        	 $cfg = new config;

            $timetable = new timetablemodel();

            $Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(),  $cfg->getAppInit()];


            if($timetable->reset() === true){

	            new Redirect("{$Outputdata[3]}timetable");

        	}else{

        		new Redirect("{$Outputdata[3]}timetable");
        	
        	}

        }		

        public function tolerance(){

        	$cfg = new config;

        	$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(),  $cfg->getAppInit()];

        	if(isset($_POST['tolerance'])){

        		if($_POST['tolerance'] >= 5 || $_POST['tolerance'] <= 50){

        			$timetable = new timetablemodel();

        			$timetable->setTolerance($_POST['tolerance']);
        		}

        	}

        	new Redirect("{$Outputdata[3]}timetable");

        }

        public function v(){

        	$cfg = new config;

			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $cfg->getAppInit()];

        	View::render('timetable_core_view/daywithoutreset.php',$Outputdata);
        }
	}

?>