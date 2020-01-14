<?php
error_reporting(E_ALL);

$appId          = 'default';

$shareDir       = '/var/www/share/';
$configPath     = $shareDir . 'apps/' . $appId . '/config/';

//require '../includes/errors.php';
require '../includes/config.php';

$config         = $getConfig($configPath);

$vendorPath     = $shareDir . 'lib/vendor/';

try {

    $globalDI = new Phalcon\Di\FactoryDefault();

    /* CONFIG */
    $globalDI->set('config',$config,TRUE);

    /* INYECTIONS */
    require '../includes/inyections.php';

    $server = 'app.web.001';
    if(isset($_ENV['HOSTNAME'])){

        $server = $_ENV['HOSTNAME'];
    }

    /* TODO 2020
    //REQUEST ID (ACCESS ID)
    if($globalDI->get('request')->hasServer('HTTP_X_REQUEST_ID')){

        $accid = $globalDI->get('request')->getServer('HTTP_X_REQUEST_ID');
    }else{

        $accid = Nubesys\Platform\Util\Utils::getU36($globalDI);
    }
    */

    $globalDI->get('global')->set('server',$server);
    //$globalDI->get('global')->set('sesid',$di->get('session')->getId());
    //$globalDI->get('global')->set('accid',$accid);

    /* TODO 2020
    $logger->setServer($di->get('global')->get('server'));
    $logger->setSesid($di->get('global')->get('sesid'));
    $logger->setAccid($di->get('global')->get('accid'));
    */

    /* RESPONSE OBJECT */
    $uri                        = $globalDI->get('request')->getURI();

    $globalDI->get('router')->handle($uri);

    $uriParams                  = $globalDI->get('router')->getParams();
    
    $responseRedirect           = false;
    $responseObject             = new Nubesys\Core\Response\WebResponse($globalDI);
    
    if(isset($uriParams[0]) && in_array($uriParams[0], array('api','uip','uid','file'))){
        
        $requestType            = $uriParams[0];
        
        $controller = "core-m404";

        switch($requestType){

            case 'api' :
                $controller             = $uriParams[1] . "-ws";
                $responseObject         = new Nubesys\Core\Response\DataResponse($globalDI);
                break;
            
            case 'uip' :
                $controller             = $uriParams[1] . "-ui";
                $responseObject         = new Nubesys\Core\Response\WebResponse($globalDI);
                break;

            case 'uid' :
                $controller             = $uriParams[1] . "-ui";
                $responseObject         = new Nubesys\Core\Response\DataResponse($globalDI);
                break;

            case 'bin' :
                $controller             = $uriParams[1] . "-bin";
                $responseObject         = new Nubesys\Core\Response\FileResponse($globalDI);
                break;
        }
        
        $globalDI->set('responseObject', $responseObject, TRUE);

        $namespace              = implode("\\",array_map(function ($e){ return \Phalcon\Text::camelize($e);}, array('nubesys', $uriParams[1], 'controllers')));

        $globalDI->get('router')->setDefaults(
            [
                "controller" => $controller,
                "namespace" => $namespace
            ]
        );

        $application = new Phalcon\Mvc\Application();

        $application->setDI($globalDI);

        $application->useImplicitView(false);

        $application->handle($uri)->getContent();

    }else{

        $responseObject->setHtml("PAGE NOT FOUND - INVALID MODULE");
    }

    /* HEADERS */
    $headers = $responseObject->getHeaders();
    foreach($headers as $key=>$value){
        
        header($key . ": " . $value);
    }

    if($responseObject->getType() == "web"){
            
        /* REDIRECT */
        if($responseObject->hasRedirect()){
            
            $responseRedirect = $responseObject->getRedirect();
        }
        
        /* COOKIES */
        $cookies = $responseObject->getCookies();
        foreach($cookies as $key=>$value){
            
            setcookie( $key , $value, time() + 3600, "/", '', false, true);
        }
    }

    if($responseRedirect === false){
        
        echo $responseObject->getBody();
    }else{

        header("Location: " . $responseRedirect);
        exit();
    }

    exit();
    $uripartes              = explode("/", $uri);



    $controller             = $uripartes[1] . '-' . (($uripartes[1] == "ui" || $uripartes[1] == "uiws") ? "ui" : $uripartes[1]);
    $namespace              = implode("\\",array_map(function ($e){ return \Phalcon\Text::camelize($e);}, array('nubesys', $uripartes[2], 'controllers')));

    var_dump($controller);
    var_dump($namespace);

    $globalDI->get('router')->handle($globalDI->get('request')->getURI());
    
    var_dump($globalDI->get('router')->getParams());
    exit();
    $application = new Phalcon\Mvc\Application();

    $application->setDI($di);

    $application->useImplicitView(false);

    echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {

    echo $e->getMessage();
}

exit("asdasd");
/*/
if(isset($_GET['profile'])){

    if($_GET['profile'] == 'on'){

        xhprof_enable();
    }
}
//*/

$nbsdir     = '/var/www/nubesys/';
$initpath   = $nbsdir . 'nubesys40.ini.php';

$initdata   = include $initpath;
$initkey    = $_SERVER['SCRIPT_FILENAME'];

if(is_array($initdata) && isset($initdata[$initkey])){

    $init = $initdata[$initkey];

    $vendorPath = $init['vendorpath'];
    $configPath = $init['configpath'];
    $loggerPath = $init['loggerpath'];

    require $configPath . "config.php";

    $config = $getConfig($configPath);



    try {

        $di = new Phalcon\Di\FactoryDefault();

        /* CONFIG */
        $di->set('config',$config,TRUE);

        if($di->get('config')->loader->enviroment == 'dev'){

            $loggerPath = $init['devloggerpath'];
        }

        require '../inyections/request.php';

        require '../inyections/crypt.php';

        require '../inyections/cookies.php';

        require '../inyections/logger.php';

        require '../inyections/eventsmanager.php';

        require '../inyections/loaders.php';

        require '../inyections/redis.php';

        require '../inyections/session.php';

        /*
        echo date('Y-m-d H:i:s') . "<br />";
        echo $_SERVER['HTTP_X_REQUEST_ID'] . "<br />";
        echo $_SERVER['HTTP_X_REAL_IP'] . "<br />";
        echo $_SERVER['HTTP_X_FORWARDED_FOR'] . "<br />";

        echo $di->get('session')->getId() . "<br />";

        if($di->get('session')->has('count')){

            $di->get('session')->set('count', $di->get('session')->get('count') + 1);
        }else{

            $di->get('session')->set('count', 0);
        }

        echo $di->get('session')->get('count') . "<br />";
        exit();
        */
        require '../inyections/global.php';

        require '../inyections/cacher.php';

        require '../inyections/db.php';

        require '../inyections/router.php';

        require '../inyections/assets.php';

        require '../inyections/url.php';

        require '../inyections/viewServices.php';

        require '../inyections/view.php';

        /*
        Seteo de variables Globales

        server: nombre del servidor o http worker apache2
        sesid:  id de la session
        accid:  id del request
        */

        $server = 'app.web.001';
        if(isset($_ENV['HOSTNAME'])){

            $server = $_ENV['HOSTNAME'];
        }

        if($di->get('request')->hasServer('HTTP_X_REQUEST_ID')){

            $accid = $di->get('request')->getServer('HTTP_X_REQUEST_ID');
        }else{

            $accid = Nubesys\Platform\Util\Utils::getU36($di);
        }

        $di->get('global')->set('server',$server);
        $di->get('global')->set('sesid',$di->get('session')->getId());
        $di->get('global')->set('accid',$accid);

        $logger->setServer($di->get('global')->get('server'));
        $logger->setSesid($di->get('global')->get('sesid'));
        $logger->setAccid($di->get('global')->get('accid'));

        $application = new Phalcon\Mvc\Application();

        $application->setDI($di);

        $application->useImplicitView(false);

        echo $application->handle()->getContent();
        /*/
        if(isset($_GET['profile'])){

            if($_GET['profile'] == 'on'){

                $xhprof_data = xhprof_disable();

                //display raw xhprof data for the profiler run
                //print_r($xhprof_data);

                $XHPROF_ROOT = realpath(dirname(__FILE__) .'/../..');
                include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
                include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
                // save raw data for this profiler run using default
                // implementation of iXHProfRuns.
                $xhprof_runs = new XHProfRuns_Default();
                // save the run under a namespace "xhprof_foo"
                $xhprof_runs_id = $xhprof_runs->save_run($xhprof_data, "nbs40profile");
                //echo "---------------\n".
                //"Assuming you have set up the http based UI for \n".
                //"XHProf at some address, you can view run at \n".
                //"http://<xhprof-ui-address>/index.php?run=$run_id&source=xhprof_foo\n".
                //"---------------\n";

                $di->get('logger')->debug("http://sp.tkff.co/xhprof_html/index.php?run=$xhprof_runs_id&source=nbs40profile", "profile");

            }
        }
        //*/
    } catch (Phalcon\Exception $e) {

        echo $e->getMessage();
    }
}else{

    echo "init data not found";
}

?>
