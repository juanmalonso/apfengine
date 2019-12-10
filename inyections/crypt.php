<?php

/* CACHER */
$di->set('crypt',function () use ($di){

    $crypt = new Phalcon\Crypt();
    $crypt->setKey($di->get('config')->crypt->privatekey); //Use your own key!
    return $crypt;
},true);

?>
