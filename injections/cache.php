<?php

/* REDIS CACHE */
$globalDI->set("cache", function () use ($globalDI) {
    
    if($globalDI->get('config')->main->cache->enabled){

        $connectionName         = $globalDI->get('config')->main->cache->connection;

        if(!is_null($globalDI->get('config')->connections->$connectionName)){

            $connectionData     = $globalDI->get('config')->connections->$connectionName;

            $cacheOptions       = [
                'lifetime'          => $connectionData->lifetime,
                'host'              => $connectionData->host,
                'port'              => $connectionData->port,
                'index'             => $connectionData->db,                    
            ];

            $adapter = new \Phalcon\Cache\Adapter\Redis(new \Phalcon\Storage\SerializerFactory(), $cacheOptions);

            return new \Phalcon\Cache($adapter);
        }else{

            //TODO bad Config Param
            return false;
        }
    }else{

        //TODO not service
        return false;
    }

}, true);

?>