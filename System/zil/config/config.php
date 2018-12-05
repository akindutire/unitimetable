<?php
namespace zil\config;
use zil\App;
class  Config extends App{

    protected $dbParams = null;

    protected  $curAppPath = null;
    protected  $urlRedirects = [];
    protected  $eventLog = true;
    
    protected $requestBase = null;
    protected  $curSysPath = null;

    protected $viewPath = 'view/';
    protected $sharedPath = null;
    protected $logPath = ['log/user','log/sys'];

    public function __construct(){
       
        $this->dbParams = parent::$databaseParams0;
        $this->curAppPath = parent::$curAppPath0;
        $this->urlRedirects = parent::$urlRedirects0;
        $this->eventLog = parent::$eventLogging0;
        $this->requestBase = parent::$requestBase0;
        
        $this->curSysPath = parent::$curSysPath0;

        $server_root = str_replace(DIRECTORY_SEPARATOR, "/", $_SERVER['DOCUMENT_ROOT']);
        $src_path = str_replace($server_root, null,  dirname(parent::$curAppPath0));

        if (isset($_SERVER['REQUEST_SCHEME']))	
            $scheme = $_SERVER['REQUEST_SCHEME'] === 'https' ? 'https' : 'http';
        else
            $scheme = 'http';

        $this->sharedPath =  "{$scheme}://{$_SERVER['HTTP_HOST']}".$src_path."/shared/";
    }

}

?>