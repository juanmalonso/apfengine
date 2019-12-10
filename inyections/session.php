<?php

/* SESSION */
$session = new \Nubesys\Platform\Session\Session($di);
$di->set('session',$session,true);
?>
