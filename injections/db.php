<?php

/* DATABASE ADAPTER */

$globalDI->set('datadb', function() use ($globalDI) {

    $db = new Phalcon\Db\Adapter\Pdo\Mysql((array)$globalDI->get('config')->connections->mydata);

    return $db;

},true);

$globalDI->set('utildb', function() use ($globalDI) {

    $db = new Phalcon\Db\Adapter\Pdo\Mysql((array)$globalDI->get('config')->connections->myutil);

},true);
