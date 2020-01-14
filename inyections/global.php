<?php

/* GLOBAL */

$globalDI->set('global',function () use ($globalDI){

    $global = new Nubesys\Core\Register();
    
    return $global;
},true);

?>