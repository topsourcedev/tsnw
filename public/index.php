<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
ini_set('error_reporting', E_ALL);
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/Rome');

function printw($key) {
    echo WolfMVC\Registry::get("language")->sh($key);
}

ob_start();


define("APP_PATH", dirname(dirname(__FILE__)));
$uri = $_SERVER['REQUEST_URI'];
$uri = explode("public", $uri, 2);
define("SITE_PATH", $uri[0] . "public/");

require("../wolfmvc/core.wolfMVC.php");
require("../wolfmvc/utils.wolfMVC.php");
WolfMVC\Core::initialize();
WolfMVC\Registry::set("censor", new \WolfMVC\Censor());

$configuration = new WolfMVC\Configuration(array(
  "type" => "ini"
  ));
try {
    WolfMVC\Registry::set("configuration", $configuration->initialize());
    WolfMVC\Registry::get("configuration")->initialize()->parse(APP_PATH . "/application/configuration/prova");
}
catch (\Exception $e) {
    die('Si &eacute; verificato un errore.<br> ' . $e->getMessage());
}

try {
    foreach (\WolfMVC\Censor::get("init_database") as $key => $idb) {
        $database = new WolfMVC\Database();
        $db = \WolfMVC\Censor::get("database");
        WolfMVC\Registry::set("database_" . $db[$idb][0], $database->initialize($db[$idb][1]));
    }
}
catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
}
catch (Exception $e) {
    echo $e->getMessage();
}
try {
    foreach (\WolfMVC\Censor::get("language") as $key => $lang) {
        $lang = new WolfMVC\Lang($lang);
        WolfMVC\Registry::set("language", $lang);
        WolfMVC\Base::$_lang = WolfMVC\Registry::get("language");
    }
}
catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
}
catch (Exception $e) {
    echo $e->getMessage();
}
try {
    foreach (\WolfMVC\Censor::get("module") as $key => $mod) {
        if (is_file(APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini")) {
            $array = WolfMVC\Registry::get("module_" . $mod[1]);
            if (!is_array($array))
                $array = array("conf" =>APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini");
            WolfMVC\Registry::set("module_" . $mod[1], $array);
        }
    }
}
catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
}
catch (Exception $e) {
    echo $e->getMessage();
}
try {
    $cache = new WolfMVC\Cache();
    WolfMVC\Registry::set("cache", $cache->initialize());
}
catch (\Exception $e) {
    echo $e->getMessage();
}

try {

    $session = new WolfMVC\Session();
    WolfMVC\Registry::set("session", $session->initialize());
}
catch (\Exception $e) {
    echo $e->getMessage();
}

//ok

try {
    $router = new WolfMVC\Router(array(
      "url" => isset($_GET["url"]) ? $_GET["url"] : "home/index",
      "extension" => isset($_GET["url"]) ? $_GET["url"] : "html"
    ));
    WolfMVC\Registry::set("router", $router);
    $router->dispatch();
}
catch (\Exception $e) {
    echo $e->getMessage();
    echo "<pre>";
    print_r($e->getTrace());
    echo "</pre>";
}




unset($configuration);
unset($database);
unset($cache);
unset($session);
unset($router);

//WolfMVC\Registry::esponi();
//WolfMVC\Censor::esponi();

ob_end_flush();
exit;
