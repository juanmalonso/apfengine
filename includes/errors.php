<?php

function nbs_error_handler($errorno, $errorm){

    $timestamp = microtime(true);
    $exit = false;
    $errort = 'E_RECOVERABLE_ERROR';

    switch ($errorno) {
        case 1:     $errort = 'E_ERROR';
                    $exit = true;
                    break;
        case 2:     $errort = 'E_WARNING';
                    break;
        case 4:     $errort = 'E_PARSE';
                    break;
        case 8:     $errort = 'E_NOTICE';
                    break;
        case 16:    $errort = 'E_CORE_ERROR'; 
                    $exit = true;
                    break;
        case 32:    $errort = 'E_CORE_WARNING';
                    break;
        case 64:    $errort = 'E_COMPILE_ERROR';
                    $exit = true;
                    break;
        case 128:   $errort = 'E_COMPILE_WARNING';
                    break;
        case 256:   $errort = 'E_USER_ERROR';
                    $exit = true;
                    break;
        case 512:   $errort = 'E_USER_WARNING';
                    break;
        case 1024:  $errort = 'E_USER_NOTICE';
                    break;
        case 2048:  $errort = 'E_STRICT';
                    break;
        case 4096:  $errort = 'E_RECOVERABLE_ERROR';
                    $exit = true;
                    break;
        case 8192:  $errort = 'E_DEPRECATED';
                    break;
        case 16384: $errort = 'E_USER_DEPRECATED';
                    break;
        case 30719: $errort = 'E_ALL';
                    $exit = true;
                    break;
        default:    $errort = 'E_UNKNOWN';
                    break;
    }

    $accid = '-';
    if(isset($_SERVER['HTTP_X_REQUEST_ID'])){

        $accid = $_SERVER['HTTP_X_REQUEST_ID'];
    }

    $errorf = '-';
    if(isset(func_get_args()[2])){

        $errorf = func_get_args()[2];
    }

    $errorl = '-';
    if(isset(func_get_args()[3])){

        $errorl = func_get_args()[3];
    }

    $error_obj = array();
    $error_obj['@timestamp']        = "$timestamp";
    $error_obj['accid']             = $accid;
    $error_obj['type']              = "error";
    $error_obj['error']             = array();
    $error_obj['error']['num']      = "$errorno";
    $error_obj['error']['label']    = $errort;
    $error_obj['error']['message']  = $errorm;
    $error_obj['error']['file']     = utf8_encode($errorf);
    $error_obj['error']['line']     = "$errorl";
    
    error_log(json_encode($error_obj, JSON_UNESCAPED_SLASHES));

    if($exit){

        exit();
    }
}

set_error_handler("nbs_error_handler");

?>