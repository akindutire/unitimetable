<?php
	
	namespace src\timetable\renderer;
	

	use \zil\factory\Redirect;
	use \zil\factory\Session;
	
	use src\timetable\config\config;

	
 
	class login{


		public function __construct(){

		}

		public function index(){


			$cfg = new config;
			$Outputdata = [$cfg->getAppPath(), 'view/asset/img/ui1.jpg', $cfg->getAppUrl(), $cfg->getAppInit()];

			if(isset($_POST['username']) && isset($_POST['password'])){

				if(!empty($_POST['username']) && !empty($_POST['password'])){

					$data_bunch = json_decode(file_get_contents("{$Outputdata[0]}database/data.json"));
					
					if($data_bunch->account->{$_POST['username']}->username == $_POST['username'] && $data_bunch->account->{$_POST['username']}->password == $_POST['password']){

						Session::buildSession([ ['App_Cert',1], ['App_Cert_Id',$_POST['username']] ]);
						
						new Redirect("{$Outputdata[3]}faculty");
					
					}else{

						new notification(false,"Invalid Login Credentials<br><a class='w3-button w3-white w3-text-blue w3-small w3-round' href='{$Outputdata[3]}'>Back</a>");
						
					}
				}

			}
			
			

		}
				
	}

?>