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
	

	if(!empty($data->course_code) && (!empty($data->venue_id) || !empty($data->time_id) || !empty($data->day_id))){

		$dmgt = new dmgt;

	
		$feedback = $dmgt->check_course_affordance($data->course_code, $data->venue_id, $data->time_id, $data->day_id, $data->size_of_uncommitted_allocation, $data->v_f_exception);
		
		if ($feedback == true)
			echo json_encode(["msg"=>$dmgt->msg,"success"=>1]);
		else
			echo json_encode(["msg"=>"An Error occured, retry","success"=>0]);
		
	}else{

		echo json_encode(["msg"=>"Course must not be empty","success"=>0]);
	}
	

?>