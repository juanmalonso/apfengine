<?php

/* ROUTER */
$router = new Phalcon\Mvc\Router();

$router->removeExtraSlashes(true);

foreach($config->router as $route){
    
    $router->add($route['pattern'],
                 array(
                        'controller' => $route['controller'],
                        'action' => $route['action'],
                        'namespace' => (isset($route['namespace'])) ? $route['namespace'] : "Web\\Controllers\\",
                      ))
                      ->setHostName((isset($route['host'])) ? $route['host'] : '(.*)')
                      ->convert('params', function ($params) use ($route) {
                          
                          $params = (isset($route['preparams'])) ? implode('/',(array)$route['preparams']) . '/' . $params : $params;
                          
                          $params = (isset($route['posparams'])) ? $params . '/' . implode('/',(array)$route['posparams']) : $params;
                          
                          if(isset($route['nameparams'])){
                              
                              foreach($route['nameparams'] as $param=>$value){
                                  
                                  $params .= '/' . $param . ':' . $value;
                              }
                          }
                          
                          return $params;
                      });
}

$di->set('router',$router,TRUE);

?>