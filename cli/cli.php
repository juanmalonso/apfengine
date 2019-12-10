<?php
//require '../includes/errors.php';

error_reporting(E_ALL);
/*
 * Se Carga el Inicializador
 */

$nbsdir     = '/var/www/nubesys/';
$initpath   = $nbsdir . 'nubesys40.ini.php';

$initdata   = include $initpath;
$initkey    = __FILE__;

if(is_array($initdata) && isset($initdata[$initkey])){
    
    $init = $initdata[$initkey];

    $vendorPath = $init['vendorpath'];
    $configPath = $init['configpath'];
    $loggerPath = $init['loggerpath'];
    
    require $configPath . "config.php";

    $config = $getConfig($configPath);
    
    try {
        
        $di = new Phalcon\Di\FactoryDefault\Cli();
        
        /* CONFIG */
        $di->set('config',$config,TRUE);

        if($di->get('config')->loader->enviroment == 'dev'){

            $loggerPath = $init['devloggerpath'];
        }

        require '../inyections/crypt.php';
        
        require '../inyections/logger.php';

        require '../inyections/eventsmanager.php';
        
        require '../inyections/loaders.php';

        require '../inyections/global.php';

        require '../inyections/redis.php';
        
        require '../inyections/cacher.php';
        
        //require '../inyections/tracker.php';

        require '../inyections/db.php';

        $di->get('global')->set('server',(isset($server)) ? $server : "nbs001");
        $di->get('global')->set('pspid',getmypid());
        $di->get('global')->set('sesid',session_id());
        $di->get('global')->set('accid',Nubesys\Platform\Util\Utils::getU36($di));

        $logger->setServer($di->get('global')->get('server'));
        $logger->setSesid($di->get('global')->get('sesid'));
        $logger->setAccid($di->get('global')->get('accid'));
        
        $application = new \Phalcon\CLI\Console();
        
        $application->setDI($di);
        
        $arguments = array();
        $arguments['params'] = array();
        
        $arguments = array();
        $arguments['task']      = $argv[2];
        $arguments['action']    = 'run';

        foreach($argv as $k => $arg) {

            if($k >= 3){

                $arguments['params'][] = $arg;
            }
        }

        // define global constants for the current task and action
        define('CURRENT_TASK', 'process');
        define('CURRENT_ACTION', 'run');
        
        $application->handle($arguments);
        
    } catch (Phalcon\Exception $e) {
	
        echo $e->getMessage();
    }
}else{
    
    echo "init data not found";
}