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
	use \src\timetable\model\timetablemodel as tmt;

	$config = new cfg;	
	
	$App = new App($config->getAppPath(), $config->getDatabaseParams(), $config->redirects(), false);

	

	$data = json_decode(file_get_contents("php://input"));
	
	if(!empty($data->c_code)  && $data->tolerance>=0 && !empty($data->day)){

		$sanitize = new Sanitize;
		$array = $sanitize->cleanData([$data->c_code,$data->day,$data->tolerance, $data->check_clashes]);
		
		$tmt = new tmt;

		$feedback = $tmt->fix_course($array[0],$array[1],$array[2], $array[3]);
		
		if ($feedback == true)
			echo json_encode(["msg"=>"Course Fixed","success"=>1]);
		else
			echo json_encode(["msg"=>$tmt->msg,"success"=>0]);
		
	}else{

		echo json_encode(["msg"=>"Some field must not be empty","success"=>0]);
	}

?>