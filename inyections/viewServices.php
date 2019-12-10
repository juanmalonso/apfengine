<?php
/* VOLT SERVICE */

$di->set('voltService', function($view, $di) use ($config) {

    $volt = new Phalcon\Mvc\View\Engine\Volt($view, $di);

    $volt->setOptions(
        array(
            "compiledPath"      => $config->web->voltcompilepath,
            "compiledExtension" => $config->web->voltcompileext
        )
    );

    return $volt;

},true);

/* PHP SERVICE */

$di->set('phpService', function($view, $di) use ($config) {

    $php = new Phalcon\Mvc\View\Engine\Php($view, $di);
    /*
    $php->setOptions(
        array(
            "compiledPath"      => $config->web->voltcompilepath,
            "compiledExtension" => $config->web->voltcompileext
        )
    );
    */
    return $php;

},true);