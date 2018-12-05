<?php
namespace src\timetable\config;

/**
 *   App Configuration
 */

class config{

	private static $DB_DRIVER 	    =   'mysql';
	private static $DB_HOST 		=   'localhost';
	private static $DB_USER 		=   'root';
	private static $DB_PASSWORD 	=   '';
	private static $DB_NAME 		=   'oautimetable';
	private static $DB_PORT 		=    3306;
	
	private static $APP_ABSPATH 	= 	null;
	private static $APP_ROOTPATH 	= 	null;


	public function __construct(){

	    $this->setPaths();    
    }

    private function setPaths(){
    	
		$server_root = str_replace(DIRECTORY_SEPARATOR, "/", $_SERVER['DOCUMENT_ROOT']);
		
        $resolved_path = str_replace($server_root, null, (str_replace(DIRECTORY_SEPARATOR, "/", dirname(__DIR__))) );
        $app_dir = str_replace(DIRECTORY_SEPARATOR, "/", $resolved_path);
		
		self::$APP_ABSPATH = $app_dir."/";
        self::$APP_ROOTPATH = $server_root."/";
	}

	public function getDatabaseParams(){

		return ['driver'=>self::$DB_DRIVER, 'host'=>self::$DB_HOST, 'user'=>self::$DB_USER, 'password'=>self::$DB_PASSWORD, 'database'=>self::$DB_NAME, 'port'=>self::$DB_PORT];
	}
	
	public function getRootPath(){

	    return self::$APP_ROOTPATH;
    }

	public function getAppPath(){

		return self::$APP_ROOTPATH."".self::$APP_ABSPATH;
	}

	public function getAppUrl(){
			
		if (isset($_SERVER['REQUEST_SCHEME']))	
			$scheme = $_SERVER['REQUEST_SCHEME'] === 'https' ? 'https' : 'http';
		else
			$scheme = 'http';

		return "{$scheme}://{$_SERVER['HTTP_HOST']}".self::$APP_ABSPATH;
    }

    public function getAppInit(){

		//return '/oautimetable/';
        return str_replace( str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace(DIRECTORY_SEPARATOR,'/', dirname(\get_included_files()[0])) ).'/';
    }

	public function redirects(){

		#Edit the redirect array to set special redirections
		return [
			["fromexample/view","toexample/view"],
			["fromexample/view2","toexample/view2"],
		];
	}
	
}
	
?>


<?php

