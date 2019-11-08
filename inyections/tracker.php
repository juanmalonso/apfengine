<?php

/* TRACKER */
$di->set('tracker',function () use ($di){

    $app = $di->get('config')->analytics->app;

    $tracker = new \Nbs\Analytics\Tracker($di, $app);

    return $tracker;
},true);

?>