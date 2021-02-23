<?php

$globalDI->set("logger", function () use ($globalDI) {

    if($globalDI->get('config')->main->logs->adapter == 'stream'){

        $formatter      = new \Phalcon\Logger\Formatter\Line('%message%');
        $mainAdapter    = new \Phalcon\Logger\Adapter\Stream('php://stdout');
        $mainAdapter->setFormatter($formatter);

        $logger = new \Phalcon\Logger(
            'messages',
            [
                'main'   => $mainAdapter
            ]
        );
        
        return new \Nubesys\Core\Logger($logger, $globalDI->get('config')->main->logs->types->toArray(), $globalDI->get('config')->main->logs->context);
    }else{

        //TODO not service
        return false;
    }
}, true);

?>