<?php

use Dotenv\Dotenv;

Dotenv::createImmutable(__DIR__ . "/../../")->load();

define('CONF_DB_HOST', $_ENV['CONF_DB_HOST']);
define('CONF_DB_PORT', $_ENV['CONF_DB_PORT']);
define('CONF_DB_USER', $_ENV['CONF_DB_USER']);
define('CONF_DB_PASS', $_ENV['CONF_DB_PASS']);
define('CONF_DB_NAME', $_ENV['CONF_DB_NAME']);
define('CONF_API_KEY', $_ENV['CONF_API_KEY']);
define("URL_BASE", "http://localhost/finance");

const DATA_LAYER_CONFIG = [
    "driver" => "mysql",
    "host" => CONF_DB_HOST,
    "port" => CONF_DB_PORT,
    "dbname" => CONF_DB_NAME,
    "username" => CONF_DB_USER,
    "passwd" => CONF_DB_PASS,
    "options" => [
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_CASE => PDO::CASE_NATURAL
    ]
];