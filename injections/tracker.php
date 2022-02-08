<?php

/* TRACKER */
$globalDI->set('tracker',function () use ($globalDI) {

    $tracker = new \Nubesys\Analytics\Tracker();
    
    $tracker->setDI($globalDI);

    return $tracker;
},true);

?>