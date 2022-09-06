<?php
	
	namespace src\timetable\renderer;
	

    use \zil\factory\View;
	use \zil\factory\Redirect;
    use \zil\factory\Session;
	
	
	use src\timetable\config\config;
	use src\timetable\model\settingsmodel;

	class settings{

		

		public function __construct(){

		}

		public function index(){

			$cfg = new config;

            $s = new settingsmodel;
            
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), null, $cfg->getAppInit(), null, null];

			View::render('settings/account.php',$Outputdata);

		}

		public function account(){

			$this->index();
        }

        public function user($param){

        	$cfg = new config;
        	
        	$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), null, $cfg->getAppInit(), null, null];

        	$s = new settingsmodel;

            $action = $param[0];
            

        	if (empty($action) || !isset($action)) {

                new Redirect("{$Outputdata[4]}settings");
        		

        	}elseif ( $action == "create") {
                
                
        		if( (isset($_POST['uname']) && isset($_POST['upass']) )  && (!empty($_POST['uname']) && !empty($_POST['upass']))  ){

        			if($s->createUser($_POST['uname'],$_POST['upass'])){

    					$template = "{$s->msg}<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[4]}settings'>Back</a>";
                        
                        new notification(true, $template);        				
                    
                    }else{

                        $template = "{$s->msg} ,Account Not Created<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[4]}settings'>Back</a>";

						new notification(false, $template);  
                    }
        			
        		}else{

        			new Redirect("{$Outputdata[4]}settings");
        		}

        	}elseif ($action == "update"){

        		if(isset($_POST['opwd']) && isset($_POST['npwd'])){

        			if( !empty($_POST['opwd']) && !empty($_POST['npwd']) ){

        				if($s->updateUser(Session::getSession('App_Cert_Id'), $_POST['opwd'], $_POST['npwd'])){
        				
                        	(new logout())->index();        				
                        
                        }else{
						  
                           $template = "{$s->msg} ,Password Not Changed<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}settings'>Back</a>";

                            new notification(false, $template); 
                             
                        }

        			}
        			
        		}else{

        			new Redirect("{$Outputdata[2]}settings");
        		}

        	}


        }

        public function import($action){

            $cfg = new config;
            
            $Outputdata = [$cfg->getAppUrl(),'view/asset/img/ui1.jpg',$cfg->getAppInit(), null, $cfg->getAppInit(),null, null];

            $s = new settingsmodel;

            if (empty($action) || !isset($action)) {

                View::render('settings/nt.php',$Outputdata); 

            }


            if($action == "venue"){

               if ($s->importVenue($_FILES['file']['tmp_name'])) {
                    
                    $template = "{$s->msg}<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}venue'>Back</a>";
                        
                    new notification(true, $template);  
               }else{

                    $template = "{$s->msg}<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}venue'>Back</a>";
                        
                    new notification(false, $template);
               }

            }elseif ($action == "coursecapacity") {
                
                if ($s->importCourseCapacity($_FILES['file']['tmp_name'])) {
                    
                    $template = "{$s->msg}<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}venue'>Back</a>";
                        
                    new notification(true, $template);  
               }else{

                    $template = "{$s->msg}<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[2]}venue'>Back</a>";
                        
                    new notification(false, $template);
               }

            }else{

              new Redirect("{$Outputdata[2]}settings");

            }

        }
				
	}

?>