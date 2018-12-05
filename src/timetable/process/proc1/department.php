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
	
	if(!empty($data->faculty) && !empty($data->department) && !empty($data->time) && !empty($data->department_short_code)){

		$sanitize = new Sanitize;
		$array = $sanitize->cleanData([$data->faculty,$data->department,$data->time,$data->department_short_code]);
		
		$dmgt = new dmgt;

		$feedback = $dmgt->adddepartment($array[0],ucwords(strtolower($array[1])),$array[2],strtoupper($array[3]));
		
		if ($feedback == true)
			echo json_encode(["msg"=>$dmgt->msg[1],"success"=>1,"id"=>$dmgt->msg[0]]);
		else
			echo json_encode(["msg"=>"Couldn't add Department, retry","success"=>0]);
		
	}else{

		echo json_encode(["msg"=>"Department must not be empty","success"=>0]);
	}


?>