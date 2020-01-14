<?php

foreach($config->inyections as $inyection){

    require "../inyections/" . $inyection . ".php";
}

?>