<?php

namespace src\timetable\process;
/*
	@params include  - File inclusion is not relative to Node init
*/
include_once("includes.php");	
		

	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Logger;
	use \zil\factory\Session;
	use \zil\factory\Sanitize;
	use \zil\factory\Security;
	use \zil\factory\Mailer;
	use \zil\factory\Redirect;
	use \zil\factory\Fileuploader;
	use \zil\factory\Filehandler;
	use zil\App;


	use \src\timetable\config\config as cfg;
	use \src\timetable\model\datamanagementmodel as dmgt;
	

	$config = new cfg;
	
	
	$InApp = new App($config->getAppPath(), $config->getDatabaseParams(), $config->redirects(), false);
	
	$data = json_decode(file_get_contents("php://input"));
	
	if(!empty($data->course_code) && !empty($data->title) && !empty($data->hours_req) && !empty($data->straight_hours)){

		$sanitize = new Sanitize;
		$array = $sanitize->cleanData([$data->course_code,$data->title,$data->hours_req,$data->straight_hours]);
		
		$dmgt = new dmgt;

		$feedback = $dmgt->edit_basic_course_details($array[0],$array[1],$array[2],$array[3],$data->timebound,intval($data->totalRegistered));
		
		if ($feedback == true){
			echo json_encode(["msg"=>$dmgt->msg,"success"=>1]);
		}else{
			
			$error_msg = $dmgt->msg;

			if ($dmgt->msg === 0 )
				$error_msg = "Couldn't edit course, retry";
			
				

			echo json_encode(["msg"=>$error_msg,"success"=>0]);
		}
		
	}else{

		echo json_encode(["msg"=>"Some field must not be empty","success"=>0]);
	}


?>