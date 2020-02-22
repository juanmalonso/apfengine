<?php

/* SESSION */
$globalDI->set('session',function () use ($globalDI){

    $session = new \Nubesys\Core\Session\Session($globalDI);

    return $session;
},true);

?>
