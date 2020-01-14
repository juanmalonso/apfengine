<?php

/* ROUTER */
$router = new Phalcon\Mvc\Router();

$router->removeExtraSlashes(true);

foreach($globalDI->get('config')->routing->routes as $route){
    
    $routerOptions                      = array();
    //ACTION
    $routerOptions['action']            = (property_exists($route, 'action')) ? $route->action : "route";

    //CONTROLLER
    if(property_exists($route, 'controller')){

        $routerOptions['controller']    = $route->controller;
    }

    //NAMESPACE
    if(property_exists($route, 'namespace')){

        $routerOptions['namespace']    = $route->namespace;
    }

    $router->add("{params:" . $route->pattern . "}", $routerOptions)
    ->convert('params', function ($params) use ($route){
        
        $params = (isset($route['preparams'])) ? implode('/',(array)$route['preparams']) . $params : $params;

        $params = (isset($route['posparams'])) ? $params . '/' . implode('/',(array)$route['posparams']) : $params;

        if(isset($route['nameparams'])){
                              
            foreach($route['nameparams'] as $param=>$value){
                
                $params .= '/' . $param . ':' . $value;
            }
        }
        
        return $params;
    });
}

$globalDI->set('router',$router,TRUE);

?>