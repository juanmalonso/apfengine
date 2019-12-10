<?php

/* LOADERS */
$files = array();
$namespaces = array();
$classmap = array();
$prefixes = array();

$composer_files = array();
$composer_classmap = array();
$composer_psr4_prefixes = array();

$path = $vendorPath . 'composer/';

$composer_files = require $path . 'autoload_files.php';
$composer_classmap = require $path . 'autoload_classmap.php';
$composer_namespaces = require $path . 'autoload_namespaces.php';
$composer_psr4_prefixes = require $path . 'autoload_psr4.php';

foreach($composer_files as $key=>$value){

    $files[] = $value;
}

foreach($composer_classmap as $key=>$value){

    $classmap[$key] = $value;
}


foreach($composer_namespaces as $key=>$value){

    if(strpos($key, '_')){

        $prefixes[$key] = $value[0] . '/';
    }else{

        $namespaces[$key] = $value[0];
    }

}

foreach($composer_psr4_prefixes as $key=>$value){

    $key = substr($key, 0, strlen($key) -1);

    $namespaces[$key] = $value[0];
}

if($di->get('config')->loader->enviroment == 'dev'){

    foreach($namespaces as $nms=>$dir){

        if(strpos('nbs40-',$dir) != -1){

            foreach ((array)$di->get('config')->loader->devreplacepaths as $oldpath=>$newpath){

                $namespaces[$nms] = str_replace($oldpath,$newpath,$namespaces[$nms]);
            }
        }
    }
}

$loader = new Phalcon\Loader();

$loader->registerFiles($files);

$loader->registerNamespaces(
    array_merge($namespaces,
        array(
           'Web\Controllers'   => __DIR__.'/../app/controllers/'
        ),
        (array)$di->get('config')->loader->namespaces
    )
);

//print_r($loader->getNamespaces());exit();

$loader->registerClasses($classmap);

$dirs = array();

$dirs[] = __DIR__."/../app/tasks";

$loader->registerDirs($dirs);

$eventsManager->attach('loader', function ($event, $loader) {

   if ($event->getType() == 'beforeCheckPath') {
        //echo "<hr /> Check - " . $loader->getCheckedPath() . "\r\n";
   }
});

$loader->setEventsManager($eventsManager);

$loader->register();

$di->set('loader',$loader,TRUE);
