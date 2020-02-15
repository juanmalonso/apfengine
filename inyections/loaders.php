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

if($globalDI->get('config')->main->enviroment == 'dev'){

    foreach($globalDI->get('config')->loader->apfclassespaths as $replacepath){
        
        foreach($namespaces as $namespace=>$path){

            if(strpos($replacepath['propath'], $path) != -1){

                $namespaces[$namespace] = str_replace($replacepath['propath'], $replacepath['devpath'],$namespaces[$namespace]);
            }
        }
    }
}

$loader = new Phalcon\Loader();

$loader->registerFiles($files);


$loader->registerNamespaces(
    array_merge($namespaces,
        (array)$globalDI->get('config')->loader->namespaces
    )
);

//print_r($loader->getNamespaces());

$loader->registerClasses($classmap);

$dirs = array();

//$dirs[] = __DIR__."/../app/tasks";

$loader->registerDirs($dirs);

$globalDI->get('eventsManager')->attach('loader', function ($event, $loader) {

   if ($event->getType() == 'beforeCheckPath') {
        //echo "<hr /> Check - " . $loader->getCheckedPath() . "\r\n";
   }
});

$loader->setEventsManager($globalDI->get('eventsManager'));

$loader->register();

$globalDI->set('loader',$loader,TRUE);
