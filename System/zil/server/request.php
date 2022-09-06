<?php 
namespace zil\server;

use \zil\config\Config;

class Request extends Config{

    private $request = null;

    private $renderer = 'home';
    private $view   =   'index';

    public function __construct(){ }

    public function getRequest(string $requestkey){

        $cfg = new Config;
       
        if(isset($_REQUEST[$requestkey])){
            $request = $this->sanitizeRequest($_REQUEST[$requestkey]);
        }else{
           
            #this mimics the htacess incase it's not enabled or we use the php built-in webserver
            $base = $cfg->requestBase;
            
            $request = rtrim($_SERVER['REQUEST_URI'],'/');


            if($base != '/')
                $request = str_replace($base, '',  rtrim($_SERVER['REQUEST_URI'],'/').'/'  );

            $request = $this->sanitizeRequest($request);
        }
        
        $redirects = $cfg->urlRedirects;
        if(count($redirects) != 0){
            foreach($redirects as $k => $url){

                if(!is_string($k))
                    continue;
                
                if(preg_match($k,$request) == 1){
                    return $url;
                    break;
                }
            }
        }
        return $request;
    }

    private function sanitizeRequest($request){

        //[a-zA-Z0-9]
        $pattern = '/^(\S+\/?)+$/';
        if (preg_match($pattern, $request )===1) {
            
            if( count(explode('/',trim($request,'/'))) == 1)
                return trim($request,'/')."/".$this->view;
            
            return trim($request,'/');
        }else{
            return "$this->renderer/$this->view";
        }
    }

    

}

?>