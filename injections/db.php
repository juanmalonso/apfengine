<?php

/* DATABASE ADAPTER */

$globalDI->set('datadb', function() use ($globalDI) {

    $connectionName         = $globalDI->get('config')->main->datadb->connection;

    if(!is_null($globalDI->get('config')->connections->$connectionName)){

        $connectionData     = $globalDI->get('config')->connections->$connectionName;
        
        $dbOptions       = [
            'host'          => $connectionData->host,
            'username'      => $connectionData->user,
            'password'      => $connectionData->pass,
            'dbname'        => $connectionData->name,                    
        ];
        
        return new Phalcon\Db\Adapter\Pdo\Mysql($dbOptions);
    }else{
        
        return false;
    } 

},true);

$globalDI->set('utildb', function() use ($globalDI) {

    $db = new Phalcon\Db\Adapter\Pdo\Mysql((array)$globalDI->get('config')->connections->myutil);

    return $db;

},true);
