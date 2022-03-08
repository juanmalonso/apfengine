<?php

/* REDIS INSTANCE */
$globalDI->set('redisInstance',function () use ($globalDI){

    $redis = new \Redis();
    
    if($redis->connect($globalDI->get('config')->connections->redis->host,
                       $globalDI->get('config')->connections->redis->port,
                       $globalDI->get('config')->connections->redis->lifetime)){
        
        if(!$redis->auth($globalDI->get('config')->connections->redis->lifetime)){

            $redis = false;
        }
        
    }else{

        $redis = false;
    }

    return $redis;
},true);

?>