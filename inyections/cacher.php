<?php

/* CACHER */
$di->set('cacher',function () use ($di){

    $cacher = new \Nubesys\Platform\Cache\Cacher($di);

    return $cacher;
},true);

?>