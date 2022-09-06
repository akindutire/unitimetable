<?php
	
	namespace zil\factory;	

	use \zil\config\Config;
	
	class View extends Config{

        private static $data = null;

		public function __construct(){}
        
        public static function render($view,$_DATA = []){

            $parent = new parent;
    
            (new Filehandler())->createDir($parent->curAppPath.''.$parent->viewPath);

			if (file_exists($parent->curAppPath.''.$parent->viewPath.''.$view) === true) {

			        $FLAG = (new Security())->Encode("Anonymous_Leave_Me_Alone");
                   
                    //Session::buildSession([ ['X_105290_ANONYM_FLAG_SET',$FLAG] ]);
                    
                    #To add a page identifier
                    //output_add_rewrite_var("ANONYM_FLAG",$FLAG);

                    #Set Page data
                    self::$data = array_merge($_DATA, ['SHARED_PATH' => $parent->sharedPath, 'ROUTER_LINK'=> $parent->requestBase, 'ROUTER_BASE'=> $parent->requestBase]);

                    #render view
                    include_once("{$parent->curAppPath}{$parent->viewPath}{$view}");           
            } else {

                #render an error view
                include_once("{$parent->curSysPath}sysview/404.html");
                
                Logger::Log("Couldn't find the view file in " . $parent->curAppPath . '' . $parent->viewPath . '' . $view . "\n Suggest you create the $view inside your " . $parent->curAppPath."/view");
           
            }
        }

        public static function getInfo(){

            return self::$data;
        }
	}

?>