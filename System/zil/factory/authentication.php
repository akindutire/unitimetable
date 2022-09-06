<?php
	
namespace zil\factory;	

	use \zil\config\Config;
	use \zil\factory\Database;
	use \zil\factory\BuildQuery;
	use \zil\factory\Session;
	use \zil\factory\Security;
	
	class Authentication extends Config{
		
		private $authdata = [ [] ];
		private $source = null;
		private $mode = null;

		public function __construct($authdata = [ [] ], $source = null, $mode = 'inline'){ 
			
			$this->authdata = $authdata;
			$this->source = $source;
			$this->mode = $mode;
		}

		public function Auth(){

			Session::deleteSession('App_Cert');
			
			if(is_null($this->source) && $this->mode == 'inline')
				return false;

			$valid = true;

			if(strtoupper($this->mode) == 'INLINE'){

				foreach($this->authdata as $authToken){

					if($authToken[0] != $authToken[1]){
						$valid = false;
						break;
					}
				}

				if($valid){
					$byte_key = bin2hex(random_bytes(32));
					Session::buildSession([ ['App_Cert', $byte_key] ]);
					return $byte_key;
				}

				return false;

			}else if(strtoupper($this->mode) == 'JSON'){

				$authObj=json_encode(file_get_contents($this->source));
				$valid = true;
				foreach($this->authdata as $k => $authToken){

					if($authObj->{$authToken[0]} != $authToken[1]){
						$valid = false;
						break;
					}
				}

				if($valid == true){
					$byte_key = bin2hex(random_bytes(32));
					Session::buildSession([ ['App_Cert', $byte_key] ]);
					return $byte_key;
				}

				return false;

			}else if(strtoupper($this->mode) == 'SQL'){

				$hash = []; 
				$first = false;
				
				foreach($this->authdata as $k => $authToken){
					
					if(count($authToken) > 2){
						if($first == false){
							$first = true;
							array_unshift($hash, $authToken);	
						}
						unset($this->authdata[$k]);
					}
				}
				
				
				$connect_handle = (new Database())->connect();
				$sql = new BuildQuery($connect_handle);

				if(count($hash) == 0)
					$rs = $sql->read($this->source, $this->authdata, [], ['LIMIT 1']);
				else
					$rs = $sql->read($this->source, $this->authdata, [ $hash[0][0] ], ['LIMIT 1']);
				
				
				if(!is_object($rs))
					return false;
				
				if($rs->rowCount() ==  1){
					
					$byte_key = bin2hex(random_bytes(32));
					if(count($hash) == 0){
						Session::buildSession([ ['App_Cert', $byte_key] ]);
						return $byte_key;
					}else{

						list($saved_hashed) = $rs->fetch();

						if( (new Security())->hashVerify($hash[0][1], $saved_hashed) ){
							Session::buildSession([ ['App_Cert', $byte_key] ]);
							return $byte_key;
						}else{
							return false;
						}
					}
				}else{
					return false;
				}
			}

		}

		public static function revoke(){
			Session::deleteSession('App_Cert');

			if(Session::buzzSession('App_Cert') == false)
				return true;
			else
				return false;
		}

		public static function valid(){

			if(Session::buzzSession('App_Cert') && strlen(Session::getSession('App_Cert')) == 64)
				return true;

			return false;
		}

	}
?>