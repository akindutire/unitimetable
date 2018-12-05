<?php
	
	namespace src\timetable\model;

	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \CliqsStudio\config\Config;
	use \zil\factory\Security;
	use \zil\factory\Session;
	use \zil\factory\Sanitize;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	use \zil\factory\Logger;
		
	use src\timetable\config\config as cfg;
	
	class settingsmodel{

		public $msg = null;

		public function __construct(){

			$cfg = new cfg;


			if(Session::getSession('App_Cert') == 1){
			
			}else{
				new Redirect($cfg->getAppInit());
			}
		}

		
		public function createUser($user,$pwd){

			$cfg = new cfg;

			$AbsPath  =   $cfg->getAppPath();

			$data_bunch = json_decode(file_get_contents("{$AbsPath}database/data.json"));
			
			if(!empty($data_bunch->account->{$user}->username)){

				$this->msg = "Username Already Existing";
				return false;
			}		

			$data = (new Sanitize())->cleanData( [$user,$pwd] );

			$newUser = [ "username" => $data[0], "password" => $data[1] ];

			$data_bunch->account->{$user} = $newUser;

			file_put_contents("{$AbsPath}database/data.json", json_encode($data_bunch,JSON_PRETTY_PRINT));
			
			$this->msg = "Successfully Added {$user} ";
			
			return true;

		}

		public function updateUser($accountKey,$opwd,$npwd){

			$cfg = new config;

			$AbsPath  =   $cfg::$APP_ABSPATH;

			$data_bunch = json_decode(file_get_contents("{$AbsPath}database/data.json"));
			
			
			if( $data_bunch->account->{$accountKey}->password == $opwd){

				$data_bunch->account->{$accountKey}->password = $npwd;

				file_put_contents("{$AbsPath}database/data.json", json_encode($data_bunch,JSON_PRETTY_PRINT));

				return true;

			}else{

				$this->msg = "Password Incorrect";
				return false;

			}		


		}

		public function importVenue($file){

			if(mime_content_type($file) != "text/plain"){

				$this->msg = "File not Supported, Course Participants not been updated";
				return false;
			}
			
			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);		

			$h = fopen($file, 'r+');

			while (!feof($h)) {
				$c = fgetcsv($h);

				$rs = $sql->read('venue', [ ['Name',$c[0] ] ], []);

				if($rs->rowCount() == 1)
					$sql->delete('venue', [ ['Name',$c[0] ] ]);



				$sql->create('venue',['null',$c[0],$c[1],$c[2],'null']);
			
			}

			fclose($h);
			
			$this->msg = "Import Successful, Venue has been updated";
			return true;
		}
		
		public function importCourseCapacity($file){
			
			if(mime_content_type($file) != "text/plain"){

				$this->msg = "File not Supported, Course Capacities not been updated";
				return false;
			}

			$connect = (new Database())->connect();
			
			$sql = new BuildQuery($connect);		

			$h = fopen($file, 'r+');

			while (!feof($h)) {
				$c = fgetcsv($h);

				$rs = $sql->read('course_offered', [ ['Course_Code',$c[0] ] ], []);

				if($rs->rowCount() > 0)
					$sql->delete('course_offered', [ ['Course_Code',$c[0] ] ]);

					$sql->create('course_offered',[$c[0],$c[1]]);

			}

			fclose($h);
			
			$this->msg = "Import Successful, Course Capacities has been updated";
			return true;
		}

	} 

?>