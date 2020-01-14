<?php

/* CRYPT */
$globalDI->set('crypt',function () use ($globalDI){

    $crypt = new Phalcon\Crypt();
    $crypt->setKey($globalDI->get('config')->crypt->privatekey);
    return $crypt;
},true);

?>
