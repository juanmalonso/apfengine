<?php
/* LOGGER */

require $loggerPath . "Logger.php";
$logger = new \Nubesys\Platform\Logger($config->logger->logsoutput, $config->logger->logspath, $config->logger->logslevel, $config->logger->context);

$di->set('logger', $logger, true);
?>