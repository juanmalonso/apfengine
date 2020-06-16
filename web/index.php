<?php
ini_set('default_socket_timeout', -1);

error_reporting(E_ALL);

$shareDir       = '/var/www/share/';

require $shareDir . 'apps.ini.php';

$appengine      = $defaultAppEngine;//APACHE + PHP + PHALCON (spf SWOOLE + PHP + PHALCON)
$appId          = $defaultAppId;

function replacePatternParam($_str, $p_params){

    $result = $_str;

    if(preg_match('/^\{(.*)\}$/', $_str, $matches)){

        $result = $p_params[$matches[1]];
    }

    return $result;
}

if(isset($_GET['domain']) && isset($_GET['_url'])){

    foreach($preRouters as $preRoute){

        if(preg_match($preRoute['pattern'], $_GET['domain'] . $_GET['_url'], $matches)){
            
            if(isset($preRoute['redirectTo'])){


                header("Location: " . $preRoute['redirectTo']);
                exit();
            }

            if(isset($preRoute['appIdTo']) && isset($preRoute['appEngineTo'])){

                $appengine      = replacePatternParam($preRoute['appEngineTo'], $matches);
                $appId          = replacePatternParam($preRoute['appIdTo'], $matches);
            }

            break;
        }
    }
}
var_dump($appId);
var_dump($appengine);
exit();
if(isset($_GET['domain'])){

    $domainPartes   = explode(".", $_GET['domain']);

    if(count($domainPartes) > 1){

        if(in_array($domainPartes[0], $apps)){

            $appId  = $domainPartes[0];
        }
    }
}

$configPath     = $shareDir . 'apps/' . $appId . '/config/';

//require '../includes/errors.php';
require '../includes/config.php';

$config         = $getConfig($configPath);

$vendorPath     = $shareDir . 'lib/vendor/';

try {

    $globalDI               = new Phalcon\Di\FactoryDefault();
    
    /* CONFIG */
    $globalDI->set('config',$config,TRUE);

    /* MANDATORIES INJECTIONS */
    require "../injections/loaders.php";
    require "../injections/logger.php";
    require "../injections/global.php";
    require "../injections/cache.php";
    
    //TODO: partir la logica de ejecucion segun tipo de appengine
    /* WEBINPUT */
    $requestObject = new Phalcon\Http\Request();
    $globalDI->set('requestManager',function () use ($globalDI, $requestObject){

        $requestManager = new Nubesys\Core\Request\RequestManager($globalDI, $requestObject);
        
        return $requestManager;
    },true);
    
    /* SERVER ID */
    $server = 'app.web.001';
    if(isset($_ENV['HOSTNAME'])){

        $server = $_ENV['HOSTNAME'];
    }
    $globalDI->get('global')->set('global.server',$server);

    /* REQUEST ID (ACCESS ID) */
    $headers = $globalDI->get('requestManager')->getHeaders();
    if(isset($headers['HTTP_X_REQUEST_ID'])){

        $accid = $headers['HTTP_X_REQUEST_ID'];
    }else{

        $accid = Nubesys\Core\Utils\Utils::getU36($globalDI);
    }
    $globalDI->get('global')->set('global.accid',$accid);
    
    /* ADITIONAL INJECTIONS */
    require '../includes/injections.php';
    
    $globalDI->get('global')->set('global.sesid',$globalDI->get('session')->getId());
    
    /* TODO 2020
    $logger->setServer($di->get('global')->get('server'));
    $logger->setSesid($di->get('global')->get('sesid'));
    $logger->setAccid($di->get('global')->get('accid'));
    */

    /* RESPONSE OBJECT */
    $uri                        = str_replace(":","_p_", $globalDI->get('request')->getURI());
    
    $globalDI->get('router')->handle($uri);

    $uriParams                  = $globalDI->get('router')->getParams();
    
    $redirect                   = false;
    $responseManager            = new Nubesys\Core\Response\ResponseManager($globalDI, 'web');
    
    if(isset($uriParams[0]) && in_array($uriParams[0], array('api','uip','uid','bin'))){
        
        $requestType            = $uriParams[0];

        if($globalDI->get('router')->getControllerName() == NULL){

            switch($requestType){

                case 'api' :
                    $controller             = "core-ws";
                    $namespace              = "Nubesys\\Core\\Controllers";
                    break;
                
                case 'uip' :
                    $controller             = "core-ui";
                    $namespace              = "Nubesys\\Core\\Controllers";
                    break;
    
                case 'uid' :
                    $controller             = "core-ui";
                    $namespace              = "Nubesys\\Core\\Controllers";
                    break;
    
                case 'bin' :
                    $controller             = "core-fs";
                    $namespace              = "Nubesys\\Core\\Controllers";
                    break;
            }

        }else{

            switch($requestType){

                case 'api' :
                    $controller             = $globalDI->get('router')->getControllerName() . "-ws";
                    break;
                
                case 'uip' :
                    $controller             = $globalDI->get('router')->getControllerName() . "-ui";
                    break;
    
                case 'uid' :
                    $controller             = $globalDI->get('router')->getControllerName() . "-ui";
                    break;
    
                case 'bin' :
                    $controller             = $globalDI->get('router')->getControllerName() . "-fs";
                    break;
            }

            $namespace                      = $globalDI->get('router')->getNamespaceName();
        }

        switch($requestType){

            case 'api' :
                $responseManager        = new Nubesys\Core\Response\ResponseManager($globalDI, 'data');
                break;
            
            case 'uip' :
                $responseManager        = new Nubesys\Core\Response\ResponseManager($globalDI, 'web');
                break;

            case 'uid' :
                $responseManager        = new Nubesys\Core\Response\ResponseManager($globalDI, 'data');
                break;

            case 'bin' :
                $responseManager        = new Nubesys\Core\Response\ResponseManager($globalDI, 'file');
                break;
        }
        
        $globalDI->set('responseManager', $responseManager, TRUE);

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

        //TODO : Logica de ruteo de phalcon por defecto
        $application = new Phalcon\Mvc\Application();

        $application->setDI($globalDI);

        $application->useImplicitView(false);

        $responseManager->setHtml($application->handle($uri)->getContent());
    }

    /* HEADERS */
    $headers = $responseManager->getHeaders();
    foreach($headers as $key=>$value){
        
        header($key . ": " . $value);
    }

    if($responseManager->getType() == "web"){
            
        /* REDIRECT */
        if($responseManager->hasRedirect()){
            
            $redirect = $responseManager->getRedirect();
        }
        
        /* COOKIES */
        //TODO: Falta 
        $cookies = $responseManager->getCookies();
        foreach($cookies as $key=>$value){
            
            setcookie( $key, $value, time() + 3600, "/", '', false, true);
        }
    }

    if($redirect === false){
        
        echo $responseManager->getBody();
    }else{

        header("Location: " . $redirect);
        exit();
    }

} catch (Phalcon\Exception $e) {

    echo $e->getMessage();
}

?>
