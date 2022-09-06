<?php
namespace zil\server;

class Router{

    
    public function __construct(){ }

    public static function Route(string $requestkey){
      
        new Response((new Request())->getRequest($requestkey));
    
    }
}
?>