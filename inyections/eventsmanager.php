<?php

/* EVENTS MANAGER */
$globalDI->set('eventsManager',function (){

    $eventsManager = new Phalcon\Events\Manager();

    return $eventsManager;
},true);
?>