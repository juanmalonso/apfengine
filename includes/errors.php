<?php

function nbs_error_handler($errorno, $errorm){
    global $globalDI;
    
    $datetime       = strftime("%Y-%m-%dT%H:%M:%S%z");
    $timestamp      = microtime(true);
    $exit           = false;
    $errort         = 'E_RECOVERABLE_ERROR';

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

    $accid              = '-';
    $server             = '-';
    $sesid              = '-';
    $contexts           = array("NBS");

    if($globalDI != NULL){

        if($globalDI->has('global')){

            $server         = $globalDI->get('global')->get('global.server');
            $accid          = $globalDI->get('global')->get('global.accid');
            $sesid          = $globalDI->get('global')->get('global.sesid');
        }
        
        if($globalDI->has('config')){

            $contexts   = explode("|", $globalDI->get('config')->main->logs->context);
        }
    }

    $errorf = '-';
    if(isset(func_get_args()[2])){

        $errorf = func_get_args()[2];
    }

    $errorl = '-';
    if(isset(func_get_args()[3])){

        $errorl = func_get_args()[3];
    }

    $logobj                         = array();
    $logobj['datetime']             = "$datetime";
    $logobj['timestamp']            = "$timestamp";
    $logobj['accid']                = $accid;
    $logobj['sesid']                = $sesid;
    $logobj['server']               = $server;
    $logobj['type']                 = "PHPERROR";
    $logobj['contexts']             = implode(" ",$contexts);
    $logobj['message']              = $errort . " " . $errorm;
    $logobj['errorfile']            = utf8_encode($errorf);
    $logobj['errorline']            = $errorl;
    
    $stdout = fopen('php://stdout', 'w');
    fputs($stdout, json_encode($logobj, JSON_UNESCAPED_SLASHES) . "\r\n");
    fclose($stdout);   
    
    //var_dump(json_encode($logobj, JSON_UNESCAPED_SLASHES));
    
    if($exit){

        exit("ERROR");
    }
}

function nbs_exception_handler($p_exception){
    global $globalDI;
    
    $datetime       = strftime("%Y-%m-%dT%H:%M:%S%z");
    $timestamp      = microtime(true);
    $exit           = false;

    $accid              = '-';
    $server             = '-';
    $sesid              = '-';
    $contexts           = array("NBS");
    if($globalDI != NULL){

        if($globalDI->has('global')){

            $server         = $globalDI->get('global')->get('global.server');
            $accid          = $globalDI->get('global')->get('global.accid');
            $sesid          = $globalDI->get('global')->get('global.sesid');
        }

        if($globalDI->has('config')){

            $contexts   = explode("|", $globalDI->get('config')->main->logs->context);
        }
    }

    $logobj                         = array();
    $logobj['datetime']             = "$datetime";
    $logobj['timestamp']            = "$timestamp";
    $logobj['accid']                = $accid;
    $logobj['sesid']                = $sesid;
    $logobj['server']               = $server;
    $logobj['type']                 = "PHPEXEPTION";
    $logobj['contexts']             = implode(" ",$contexts);
    $logobj['message']              = $p_exception->getCode() . " " . $p_exception->getMessage();
    $logobj['file']                 = utf8_encode($p_exception->getFile());
    $logobj['line']                 = $p_exception->getLine();
    
    $stdout = fopen('php://stdout', 'w');
    fputs($stdout, json_encode($logobj, JSON_UNESCAPED_SLASHES) . "\r\n");
    fclose($stdout);
    
    //var_dump(json_encode($logobj, JSON_UNESCAPED_SLASHES));
    
    if($exit){

        exit();
    }
}

set_error_handler("nbs_error_handler");
set_exception_handler("nbs_exception_handler");
?>
