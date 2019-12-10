<?php

/* URL */
$di->set('url', function() use ($config) {
   $url = new Phalcon\Mvc\Url();
   $url->setBaseUri('/'.$config->application->bsepath.'/');
   return $url;
},true);

?>