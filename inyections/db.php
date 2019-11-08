<?php

/* DATABASE ADAPTER */

$di->set('db', function() use ($di, $config) {

    $db = new Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host"      => $config->db->host,
        "username"  => $config->db->user,
        "password"  => $config->db->pass,
        "dbname"    => $config->db->name
    ));

    return $db;

},true);

$di->set('utildb', function() use ($di, $config) {

    $db = new Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host"      => (property_exists($config->db, 'utilhost')) ? $config->db->utilhost : $config->db->host,
        "username"  => (property_exists($config->db, 'utiluser')) ? $config->db->utiluser : $config->db->user,
        "password"  => (property_exists($config->db, 'utilpass')) ? $config->db->utilpass : $config->db->pass,
        "dbname"    => (property_exists($config->db, 'utilname')) ? $config->db->utilname : $config->db->name
    ));

    return $db;

},true);
