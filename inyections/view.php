<?php

/* VIEW */
$di->set('view', function() use ($di, $config) {

    $view = new \Phalcon\Mvc\View\Simple();

    $view->setDI($di);

    $view->registerEngines(array(
        ".phtml" => 'voltService',
        ".js" => 'voltService',
        ".css" => 'voltService',
        ".php" => "phpService"
    ));
  
    return $view;

},true);
