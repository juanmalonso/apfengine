<?php

/* CACHER */

$globalDI->set('cacher',function () use ($globalDI){

    $cacher = new \Nubesys\Core\Cache\Cacher($globalDI);

    return $cacher;
},true);

?>