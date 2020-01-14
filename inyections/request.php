<?php

/* REQUEST */
$globalDI->set('request',function () use ($globalDI){

    $request = new Phalcon\Http\Request();
    
    return $request;
},true);

?>
