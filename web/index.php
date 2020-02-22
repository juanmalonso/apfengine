<?php
error_reporting(E_ALL);

//TODO: Recibir parametros appID y appengine por url o por CLI args

$appId          = 'default';
$appengine      = 'apf';//APACHE + PHP + PHALCON (spf SWOOLE + PHP + PHALCON)

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

    //TODO: partir la logica de ejecucion segun tipo de appengine
    /* WEBINPUT */
    $requestObject = new Phalcon\Http\Request();
    $globalDI->set('requestManager',function () use ($globalDI, $requestObject){

        $requestManager = new Nubesys\Core\Request\RequestManager($globalDI, $requestObject);
        
        return $requestManager;
    },true);
    
    //REQUEST ID (ACCESS ID)
    $headers = $globalDI->get('requestManager')->getHeaders();
    if(isset($headers['HTTP_X_REQUEST_ID'])){

        $accid = $headers['HTTP_X_REQUEST_ID'];
    }else{

        $accid = Nubesys\Core\Utils\Utils::getU36($globalDI);
    }

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
    
    $redirect                   = false;
    $responseManager            = new Nubesys\Core\Response\ResponseManager($globalDI, 'web');
    
    if(isset($uriParams[0]) && in_array($uriParams[0], array('api','uip','uid','file'))){
        
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
                    $controller             = "core-bin";
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
                    $controller             = $globalDI->get('router')->getControllerName() . "-bin";
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
                $responseManager        = new Nubesys\Core\Response\ResponseManager($globalDI, 'bin');
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
