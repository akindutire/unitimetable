<?php
namespace zil;

use \zil\server\Router;
use \zil\factory\Session;
use zil\factory\Filehandler;
use zil\factory\Logger;

class App{

    protected static $curAppPath0 = null;
    protected static $databaseParams0 = [];
    protected static $urlRedirects0 = [];
    protected static $eventLogging0 = true;
    protected static $requestBase0 = null;

    protected static $curSysPath0 = null;

    
    public function __construct(...$args){

        /**
         * args[0] = curAppPath, args[1] = databaseParams, args[2] = urlRedirects, args[3] = Event logging
         */
        
        self::$curAppPath0 = $args[0]; 
        
        if($args[1] == null || count($args[1]) == 0)
            $args[1] = ['driver'=>'mysql','host'=>'localhost','user'=>'root','password'=>'','database'=>'test','port'=>3306,'file'=>''];
        else
            self::$databaseParams0 = $args[1];

        self::$urlRedirects0 = $args[2];


        
        if( !isset($args[3])) 
            self::$eventLogging0 = false;
        else
            self::$eventLogging0 = (bool)$args[3];
            
            
        self::$curSysPath0 = __DIR__.'/';
        
        self::$requestBase0 = $this->getRequestBase();

        $this->SessionInit();
    }    

    private function getRequestBase(){

        return str_replace( str_replace(DIRECTORY_SEPARATOR, '/', $_SERVER['DOCUMENT_ROOT']), '', str_replace(DIRECTORY_SEPARATOR,'/', dirname(\get_included_files()[0])) ).'/';
    }

    private function SessionInit(){

        $session_path = str_replace("\\", "/", self::$curSysPath0)."/session/";
            
            if(!is_dir($session_path)){
                (new Filehandler())->createDir($session_path);
                Logger::Log("Session folder has been created on line ".__LINE__." in ".__METHOD__);
            }
            Session::secureSession($session_path);
    }

    public function start(bool $io = true){

        
        if($io == false){
            include_once(__DIR__."/sysview/maintenance.html" );
            return false;
        }
        
        Router::Route('url_parameters');
    }

}

?>