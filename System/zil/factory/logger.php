<?php
	
	namespace zil\factory;
	use \zil\config\Config;


	class Logger extends Config{


		public $curAppPath     	 	    = 	null;
		public static $EventLog       	= 	null;
		private static $Instance 		= 	null;
		private $logOverride 			=	false;


		public function __construct(){
		
			
		}

        private static function  getInstance(){

		    if(self::$Instance == null)
		        self::$Instance = new self;

		    return  self::$Instance;
        }

        private function eventlogger($msg){
			
			$this->curAppPath = (new parent())->curAppPath;

			$realPath = (self::getInstance())->curAppPath."/".(new parent)->logPath[0];
			(new Filehandler())->createDir($realPath);
			
				dateentrypoint:

					$file = $realPath.'/'.(date('F-d-Y',time())).".log";
					
					if(!file_exists($file))
						fopen($file, 'w+');

					if(!is_readable($file))
						goto dateentrypoint;
					
				try{

					$time = date('h:i:s a',time());						
					$msg= $time." ::-->> $msg\n";

					try {
						error_log($msg,3,$file);	
					} catch (\Exception $e){ 
						$e->getMessage();
					}

				}catch(\Exception $e){
					trigger_error($e->getMessage());
				}
		}

		public static function Init(){
			(self::getInstance())->logOverride = true;
		}

		public static function kill(){
			(self::getInstance())->logOverride = false;
		}
		
		public static function Log($msg, $errmode = 0){
			
			if($errmode == 1){
				trigger_error($msg);
				die();
			}else{
				if((self::getInstance())->logOverride){
					(self::getInstance())->eventlogger($msg);
				}else{
					if ((new parent())->eventLog){ 	
						(self::getInstance())->eventlogger($msg);
					}	
				}
			}
		}
	}
?>