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
	
	if(!empty($data->venue)){

		$sanitize = new Sanitize;
		$array = $sanitize->cleanData([$data->venue]);
		
		$dmgt = new dmgt;

		$feedback = $dmgt->removevenue($array[0]);
		
		if ($feedback == true)
			echo json_encode(["msg"=>$dmgt->msg,"success"=>1]);
		else
			echo json_encode(["msg"=>"Couldn't remove Venue, retry","success"=>0]);
		
	}else{

		echo json_encode(["msg"=>"No Venue selected","success"=>0]);
	}


?>