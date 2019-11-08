<?php
/* ASSETS */

$di->set('assets', function() {
    return new Nubesys\Base\Phalcon\Assets\Manager();
},true);

?>