<?php

	namespace zil\factory;
	use \zil\config\Config;

	class Session extends Config{

			public function __construct(){

			}

            private static function is_started(){
                if(session_status() != 2 || session_status() !== PHP_SESSION_ACTIVE)
                    session_start();
            }

			private static function getAppPrefix(){

			    $syscfg = new parent;
                $current_app_prefix = basename($syscfg::$curAppPath0)."__";
                return $current_app_prefix;
			}
			
			private static function buildSession0($id,$data){

				$_SESSION[$id] = $data;

				if (!empty($_SESSION[$id])) {
					
					Logger::Log("$id Session Saved");
					return true;
					
				}else {
					
					$error = "Server Error: Line ".__LINE__." on ".__METHOD__." Session Building Failed";
					Logger::Log($error);
					return false;
				}
			}

            
			public static function buildSession($data=[[]]){

			    self::is_started();

			    $session_id  = null;
			    $session_val = null;
                $prefix = self::getAppPrefix();

				if (count($data) != 0) {  
                    $self = new self;

                    /*Session Arrays*/
					
                    foreach ($data as $as_array) {

						if (is_array($as_array) && count($as_array)==2) {
						
							$session_id = "{$prefix}{$as_array[0]}";		$session_val = $as_array[1];
                            $self->buildSession0($session_id,$session_val);

                        }else{
						
							$error = "SQL Error: Line ".__LINE__." on ".__METHOD__." Enter Nested Array as Arguement or Nested data items Must be at least 2";
                            Logger::Log($error);
						}
					}

					session_write_close();
					
                    return true;
				
				}else{

					$error = "SQL Error: Line ".__LINE__." on ".__METHOD__." Enter Nested Array as Arguement or Nested items Must be 2";
							
					Logger::Log($error);
                    session_write_close();	
                    return false;
				}		
			}

            public static function buzzSession($id){
                
                self::is_started();

                $prefix = self::getAppPrefix();
                $id = "{$prefix}{$id}";
                
                if (isset($_SESSION[$id])){
                    session_write_close();
                    return true;
                }

                    session_write_close();
                    return false;
                
            }

            
			public static function getSession($id){

                self::is_started();
                
                $prefix = self::getAppPrefix();
                $id = "{$prefix}{$id}";
                
                if (isset($_SESSION[$id])){

				    session_write_close();
                    return $_SESSION[$id];
				}else {
				
					$error = "Error Session Index {$id} Not Found";	
                    Logger::Log($error);
                    session_write_close();
                    return false;
                }		
			}

			public static function deleteSession($id){
               
                self::is_started();
                
                $prefix = self::getAppPrefix();
                $id = "{$prefix}{$id}";
                unset($_SESSION[$id]);
                session_write_close();
			
            }

			public static function secureSession($session_path){

                ini_set('session.use_strict_mode', 1);
                ini_set('session.use_only_cookies', 1);
                
                session_save_path($session_path);
              
                session_name(hash_hmac("sha512","XCliqs","&+7624='+!!'##4)Z!!^!8VVUUSuTUU"));
                self::checkSessionLifetime();
            }


            private static function checkSessionLifetime(){

             if(self::getSession('Trace_LifeTime') == null){
              
                /**
                 * No Session Switch So far
                 */
              
                self::buildSession([ ['Trace_LifeTime',time()] ]);
            }


            $current_time = time();
            $elapsed_session = self::getSession('Trace_LifeTime');
           

            /**
             * Session limit can be modified, Currently = 15minutes
             */
            
            $session_limit = 900;

            $current_session = session_id();
            if (($current_time - $elapsed_session) > $session_limit){

                    session_start();
                    if(session_regenerate_id()) {

                        $new_session = session_id();
                        if (self::getSession('Trace_LifeTime') === $elapsed_session) {
                     
                            /**
                             * Session was copied successfully, Goto Old Session remove System Auth flags(Don't Alter) and Switch to new Session
                             */

                            session_id($current_session);
                        
                            self::deleteSession('APP_CERT');
                            self::deleteSession('Trace_LifeTime');
                            self::deleteSession('Ignore_trial_check');
                            self::deleteSession('ANONYM_FLAG_SET');

                            /**
                             * Individual App Auth flag goes here, delete Auth flag to restrict access to obsolete session
                             */
                       
                            session_id($new_session);
                            session_start();
                            self::buildSession([  ['Trace_LifeTime',time()] ]);

                        }else{

                            /**
                             * Rollback
                             */

                            $new_id =   session_create_id('sessq-');
                          
                            session_id($new_id);
                            session_start();
                            self::buildSession([ ['Last_Session',1], ['Trace_LifeTime',time()] ]);
                        }
                    }
                }

                 /**
                 * Remove Obsolete Session files
                 */
                
                if(session_status() != 2)
                    session_start();

                session_gc();
            }
	}
?>