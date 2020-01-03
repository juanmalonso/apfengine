<?php
error_reporting(E_ALL);

$appId          = 'default';

$shareDir       = '/var/www/share/';
$configPath     = $shareDir . 'apps/' . $appId . '/config/';

var_dump($configPath);exit();
//require '../includes/errors.php';
require '../includes/config.php';

$config         = $getConfig($configPath);

$vendorPath     = $shareDir . 'lib/vendor/';
exit("asdasd");
/*/
if(isset($_GET['profile'])){

    if($_GET['profile'] == 'on'){

        xhprof_enable();
    }
}
//*/

$nbsdir     = '/var/www/nubesys/';
$initpath   = $nbsdir . 'nubesys40.ini.php';

$initdata   = include $initpath;
$initkey    = $_SERVER['SCRIPT_FILENAME'];

if(is_array($initdata) && isset($initdata[$initkey])){

    $init = $initdata[$initkey];

    $vendorPath = $init['vendorpath'];
    $configPath = $init['configpath'];
    $loggerPath = $init['loggerpath'];

    require $configPath . "config.php";

    $config = $getConfig($configPath);



    try {

        $di = new Phalcon\Di\FactoryDefault();

        /* CONFIG */
        $di->set('config',$config,TRUE);

        if($di->get('config')->loader->enviroment == 'dev'){

            $loggerPath = $init['devloggerpath'];
        }

        require '../inyections/request.php';

        require '../inyections/crypt.php';

        require '../inyections/cookies.php';

        require '../inyections/logger.php';

        require '../inyections/eventsmanager.php';

        require '../inyections/loaders.php';

        require '../inyections/redis.php';

        require '../inyections/session.php';

        /*
        echo date('Y-m-d H:i:s') . "<br />";
        echo $_SERVER['HTTP_X_REQUEST_ID'] . "<br />";
        echo $_SERVER['HTTP_X_REAL_IP'] . "<br />";
        echo $_SERVER['HTTP_X_FORWARDED_FOR'] . "<br />";

        echo $di->get('session')->getId() . "<br />";

        if($di->get('session')->has('count')){

            $di->get('session')->set('count', $di->get('session')->get('count') + 1);
        }else{

            $di->get('session')->set('count', 0);
        }

        echo $di->get('session')->get('count') . "<br />";
        exit();
        */
        require '../inyections/global.php';

        require '../inyections/cacher.php';

        require '../inyections/db.php';

        require '../inyections/router.php';

        require '../inyections/assets.php';

        require '../inyections/url.php';

        require '../inyections/viewServices.php';

        require '../inyections/view.php';

        /*
        Seteo de variables Globales

        server: nombre del servidor o http worker apache2
        sesid:  id de la session
        accid:  id del request
        */

        $server = 'app.web.001';
        if(isset($_ENV['HOSTNAME'])){

            $server = $_ENV['HOSTNAME'];
        }

        if($di->get('request')->hasServer('HTTP_X_REQUEST_ID')){

            $accid = $di->get('request')->getServer('HTTP_X_REQUEST_ID');
        }else{

            $accid = Nubesys\Platform\Util\Utils::getU36($di);
        }

        $di->get('global')->set('server',$server);
        $di->get('global')->set('sesid',$di->get('session')->getId());
        $di->get('global')->set('accid',$accid);

        $logger->setServer($di->get('global')->get('server'));
        $logger->setSesid($di->get('global')->get('sesid'));
        $logger->setAccid($di->get('global')->get('accid'));

        $application = new Phalcon\Mvc\Application();

        $application->setDI($di);

        $application->useImplicitView(false);

        echo $application->handle()->getContent();
        /*/
        if(isset($_GET['profile'])){

            if($_GET['profile'] == 'on'){

                $xhprof_data = xhprof_disable();

                //display raw xhprof data for the profiler run
                //print_r($xhprof_data);

                $XHPROF_ROOT = realpath(dirname(__FILE__) .'/../..');
                include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_lib.php";
                include_once $XHPROF_ROOT . "/xhprof_lib/utils/xhprof_runs.php";
                // save raw data for this profiler run using default
                // implementation of iXHProfRuns.
                $xhprof_runs = new XHProfRuns_Default();
                // save the run under a namespace "xhprof_foo"
                $xhprof_runs_id = $xhprof_runs->save_run($xhprof_data, "nbs40profile");
                //echo "---------------\n".
                //"Assuming you have set up the http based UI for \n".
                //"XHProf at some address, you can view run at \n".
                //"http://<xhprof-ui-address>/index.php?run=$run_id&source=xhprof_foo\n".
                //"---------------\n";

                $di->get('logger')->debug("http://sp.tkff.co/xhprof_html/index.php?run=$xhprof_runs_id&source=nbs40profile", "profile");

            }
        }
        //*/
    } catch (Phalcon\Exception $e) {

        echo $e->getMessage();
    }
}else{

    echo "init data not found";
}

?>
