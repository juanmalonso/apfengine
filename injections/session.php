<?php
/* SESSION */
$globalDI->set("session", function () use ($globalDI) {

    if($globalDI->get('config')->main->session->adapter == 'redis'){
        
        $sid        = NULL;
        if($globalDI->get('requestManager')->hasCookie('PHPSESSID')){

            $sid    = $globalDI->get('requestManager')->getCookie('PHPSESSID');
        }else if($globalDI->get('global')->has('global.accid')){

            $sid    = $globalDI->get('global')->get('global.accid');
        }

        //setcookie( 'PHPSESSID', $sid, time() + 3600, "/", '', false, true);

        $connectionName         = $globalDI->get('config')->main->session->connection;
        
        if(!is_null($globalDI->get('config')->connections->$connectionName)){

            $connectionData     = $globalDI->get('config')->connections->$connectionName;

            $sessionOptions       = [
                'host'              => $connectionData->host,
                'port'              => $connectionData->port,
                'index'             => $connectionData->db,                    
            ];

            $session = new \Phalcon\Session\Manager();

            $redis = new \Phalcon\Session\Adapter\Redis(new \Phalcon\Storage\AdapterFactory(new \Phalcon\Storage\SerializerFactory()), $sessionOptions);

            $session->setAdapter($redis)
                    ->setId($sid)
                    ->start();

            return $session;
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
