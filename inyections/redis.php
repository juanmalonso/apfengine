<?php

/* CACHER */
$di->set('redisInstance',function () use ($di){

    $redis = new \Nubesys\Platform\Cache\Cacher($di);

    $redis = new \Redis();
    
    if(!$redis->connect($di->get('config')->cache->redis->host,
                       $di->get('config')->cache->redis->port,
                       $di->get('config')->cache->redis->lifetime)){

        $redis = false;
    }

    return $redis;
},true);

?>