<?php

/* ROUTER */
$router = new Phalcon\Mvc\Router();

$router->removeExtraSlashes(true);

foreach($globalDI->get('config')->routing->routes->toArray() as $route){
    
    $routerOptions                      = array();
    //ACTION
    $routerOptions['action']            = (isset($route['action'])) ? $route['action'] : "route";

    //CONTROLLER
    if(isset($route['controller'])){

        $routerOptions['controller']    = $route['controller'];
    }

    //NAMESPACE
    if(isset($route['namespace'])){

        $routerOptions['namespace']    = $route['namespace'];
    }
    
    $router->add("{params:" . $route['pattern'] . "}", $routerOptions)
    ->convert('params', function ($params) use ($route){

        $params = (isset($route['preparams'])) ? implode('/',$route['preparams']) . $params : $params;

        $params = (isset($route['posparam'])) ? $params . '/' . implode('/',$route['posparam']) : $params;

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