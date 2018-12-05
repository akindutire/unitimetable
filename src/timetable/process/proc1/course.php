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
	
	$App = new App($config->getAppPath(), $config->getDatabaseParams(), $config->redirects(), false);

	

	$data = json_decode(file_get_contents("php://input"));

	
	if(strlen($data->code) != 6){
		
		echo json_encode(["msg"=>"Course Code must be 6 characters ","success"=>0]);
		
		return null;
	
	}
	
	
	if(!empty($data->department_code) && !empty($data->title) && !empty($data->code)){

		$sanitize = new Sanitize;
		$array = $sanitize->cleanData([$data->department_code,$data->title,$data->code,$data->unit]);
		
		$dmgt = new dmgt;

		$feedback = $dmgt->addcourse($array[0],$array[1],$array[2],$array[3],$data->prac);
				
		if ($feedback == 1)
			echo json_encode(["msg"=>$dmgt->msg,"success"=>1]);
		else
			echo json_encode(["msg"=>"Duplicate Record:: Couldn't add course ","success"=>0]);
		
	}else{

		echo json_encode(["msg"=>"Some fields must not be empty, Check department field","success"=>0]);
	}

?>