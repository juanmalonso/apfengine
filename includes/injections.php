<?php

foreach($config->injections as $injection){

    require "../injections/" . $injection . ".php";
}

?>