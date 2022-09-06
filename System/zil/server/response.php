<?php
namespace zil\server;

use zil\config\Config;
use zil\factory\Logger;

class Response extends Config{

    private $resquestString = null;

    public function __construct(string $resquestString){
        $this->resquestString = $resquestString;
        $this->Response();
     }

    private function Response(){
        
        $AppPath = (new Config())->curAppPath;
        $SysPath = (new Config())->curSysPath;
        
        $requestArray = explode('/',trim($this->resquestString,'/'));
        
        if (strpos($AppPath, "/src/") == false) {

            Logger::Log("Couldn't connect to your application\nPossible Cause::No /src folder found in your directory, \n Suggest you create an \src folder and store your application in there, so there zil may find it for proper link");
            include_once($SysPath."/sysview/404.html");
            exit();
        }
        
        if( !file_exists($AppPath."/renderer/{$requestArray[0]}.php") ){
            
            $this->error_page_renderer($AppPath,$SysPath);
        }

        include_once($AppPath."/renderer/{$requestArray[0]}.php");

        $renderer = $this->get_renderer_classname($AppPath, $requestArray[0]);
        $renderer = new $renderer;
        
        if( !method_exists($renderer, $requestArray[1]) ) {
            
            $this->error_page_renderer($AppPath,$SysPath);
        }

        $method = $requestArray[1];
        unset($requestArray[0], $requestArray[1]);
        $renderer->{$method}(array_values($requestArray));

    }

    private function get_renderer_classname($AppPath,$renderer){
        $apps_pos_index = strpos($AppPath, "/src/");
        $back_namespace = substr($AppPath, $apps_pos_index+1);
        $namespace = str_replace('/', '\\', $back_namespace);
        $renderer = "\\{$namespace}renderer\\{$renderer}";
        return $renderer;
    }

    private function error_page_renderer($AppPath, $SysPath){

        if(file_exists($AppPath."/view/404.html")){
            include_once($AppPath."/view/404.html");
        }else if(file_exists($AppPath."/view/404/index.html")){
            include_once($AppPath."/view/404/index.html");
        }else if(file_exists($AppPath."/view/404/index.php")){
            include_once($AppPath."/view/404/index.php");
        }else{
            include_once($SysPath."/sysview/404.html");
        }
             
        exit();
    }
    
}
?>