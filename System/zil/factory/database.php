<?php
	
namespace zil\factory;	

	use \PDO;
	use \zil\config\Config;

	
	class Database extends Config{


		private static $Instance = null;
		
        private static $status = null;
		private $link = null;
		private $con_params = [];
		private $con_attr = [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_PERSISTENT => true ];
		

		public function __construct(){
		
			$this->con_params = (new parent())->dbParams;
		}

        private static function getInstance(){

            if(self::$Instance == null)
                self::$Instance = new self;

            return self::$Instance;
        }

		private function databaseDriverDigest($driver){

			try{
				$supportedDriversArray	=	PDO::getAvailableDrivers();
				
				if (in_array($driver, $supportedDriversArray)) 
					$this->driver 	=	$driver;
				else
					throw new \Exception("Database Driver for {$driver} is not Enabled on this Server, Suggest try Installing it");

			}catch(\Exception $e){
				Logger::Log($e->getMessage());
				self::$status 	= 	$e->getMessage();
			}
		}

        /**
         * @param $con_params
         * @return null|PDO
         */

        private function newConnection($con_params){
			
			/****
			*	More Development is Needed in Future -Supported Database ARE [Mysql,Sqlite,PgSql]
			*/

			if (sizeof($con_params) > 0) {	
				if (array_key_exists('driver', $con_params) === false) {
					
					Logger::Log("Database Params array Expect a model including the following keys\ndriver\thost\tdatabase\tuser\tpassword\tport\nAnd if Sqlite database Use the following keys\ndriver\tfile\n");
					self::$status 	= 	"Couldn't Establish a Database Connection, Database Params not well modelled, Check log for details";

					return null;
				}else{
					$this->con_params = array_merge($this->con_params,$con_params);
				}
			}

			try {
				
			    $connect_handle = null;
				if ($this->con_params['driver'] == 'mysql') {
			
						if(array_key_exists('dns', $this->con_params) === false)
							$dsn = "{$this->con_params['driver']}:host={$this->con_params['host']};port={$this->con_params['port']};dbname={$this->con_params['database']}";
						else
							$dsn = $this->con_params['dns'];

						$connect_handle 	= 	new PDO($dsn, $this->con_params['user'], $this->con_params['password'], $this->con_attr);
					
				}else if ($this->con_params['driver'] == 'sqlite') {
						
						$dsn = $this->con_params['driver'].':'.$this->con_params['file'];
						$connect_handle 	= 	new PDO($dsn, $this->con_attr);

				}else if ($this->con_params['driver'] == 'pgsql') {

						if(array_key_exists('dns', $this->con_params) === false)
							$dsn = "{$this->con_params['driver']}:host={$this->con_params['host']} port={$this->con_params['port']}dbname={$this->con_params['database']} user={$this->con_params['user']} password={$this->con_params['password']}";
						else
							$dsn = $this->con_params['dns'];

						$connect_handle 	= 	new PDO($dsn, $this->con_attr);
				}
				
				if ($connect_handle != null) {
	
					return $connect_handle;
				}else{
					throw new \PDOException("Couldn't Establish a Database Connection", 1);
				}

			} catch (\PDOException $e) {
				
				Logger::Log($e->getMessage());
				self::$status 	= 	$e->getMessage();
			}

			return false;
		}

		public function connect($con_params = []){

			$this->link = (self::getInstance())->newConnection($con_params);
			if ($this->link != false) {
				
				self::$status 	=	"Connection Established";			
				return $this->link;
			}
			return null;
		}

		public function getStatus(){

			return self::$status;
		}


		public function closeConnection(){
			
			self::$status 	=	"Connection Closed";
			$this->link = null;
		}
	}
?>