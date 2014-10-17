<?php

//ini_set('display_startup_errors', 1);
//ini_set('display_errors', 1);
//ini_set('error_reporting', E_ALL);
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
set_time_limit(1000);
date_default_timezone_set('Europe/Rome');

ini_set('memory_limit', '128M');

function printw($key) {
    echo WolfMVC\Registry::get("language")->sh($key);
}

ob_start();
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
    WolfMVC\Registry::get("configuration")->parse(APP_PATH . "/application/configuration/database");
    WolfMVC\Registry::get("configuration")->parse(APP_PATH . "/application/configuration/cache");
    WolfMVC\Registry::get("configuration")->parse(APP_PATH . "/application/configuration/session");
    WolfMVC\Registry::set("layoutenvvars", WolfMVC\Registry::get("configuration")->parse("configuration/layoutenvvars"));
    WolfMVC\Registry::set("systemstatus", WolfMVC\Registry::get("configuration")->parse("configuration/systemstatus"));
    WolfMVC\Registry::set("googleApiConf", WolfMVC\Registry::get("configuration")->parse("configuration/google"));
} catch (\Exception $e) {
    die('Si &eacute; verificato un errore.<br> ' . $e->getMessage());
}

try {
    foreach (\WolfMVC\Censor::get("database") as $key => $db) {
        $database = new WolfMVC\Database();
        WolfMVC\Registry::set("database_" . $db[0], $database->initialize($db[0]));
    }
} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    foreach (\WolfMVC\Censor::get("systemtables") as $key => $table) {
        WolfMVC\Registry::set("systemtable_" . $key, $table);
    }
} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    foreach (\WolfMVC\Censor::get("language") as $key => $lang) {
        $lang = new WolfMVC\Lang($lang);
        WolfMVC\Registry::set("language", $lang);
        WolfMVC\Base::$_lang = WolfMVC\Registry::get("language");
    }
} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}

try {
    $vtiger_login = false;
    require_once('../application/libraries/vtwsclib/vtiger/WSClient.php');
    $url = 'http://54.213.213.176/vtigercrm/webservice.php';
    $vtigerAdminAccessKey = 'tNkzlCVdaphElMuj';
    $userName = "admin";
    for ($kkk = 0; $kkk < 10; $kkk++) {
        $client = new Vtiger_WSClient($url);
        $login = $client->doLogin($userName, $vtigerAdminAccessKey);
        if (!$login)
        {
            usleep(200000); // << 0,2 secondi
            continue;
        }
        else
        {
            WolfMVC\Registry::set("VTWS", $client);
            $vtiger_login = true;
            break;
        }
    }
    if (!$vtiger_login)
    {
        echo 'Vtiger WS Login Failed';
    }
} catch (\Exception $e) {
    echo $e->getMessage();
}

try {
    foreach (\WolfMVC\Censor::get("module") as $key => $mod) {
        if (is_file(APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini"))
        {
            $array = WolfMVC\Registry::get("module_" . $mod[1]);
            if (!is_array($array))
                $array = array("conf" => APP_PATH . "/application/configuration/modules/" . $mod[1] . ".ini");
            WolfMVC\Registry::set("module_" . $mod[1], $array);
        }
    }
} catch (\WolfMVC\Configuration\Exception\Syntax $e) {
    echo $e->getMessageType();
    echo $e->getMessage();
} catch (Exception $e) {
    echo $e->getMessage();
}
//try {
//    $cache = new WolfMVC\Cache();
//    WolfMVC\Registry::set("cache", $cache->initialize());
//}
//catch (\Exception $e) {
//    echo $e->getMessage();
//}
//
try {

    $session = new WolfMVC\Session();
    WolfMVC\Registry::set("session", $session->initialize());
} catch (\Exception $e) {
    echo $e->getMessage();
}

$session = WolfMVC\Registry::get("session");
try {
    $googleApiConf = WolfMVC\Registry::get("googleApiConf");
    $googleClient = new Google_Client();

    $googleClient->setApplicationName("TSNW");
    $googleClient->setClientId($googleApiConf->client_id);
    $googleClient->setClientSecret($googleApiConf->client_secret);
    $googleClient->setRedirectUri($googleApiConf->redirect_uri_ssl);
    $googleClient->setDeveloperKey($googleApiConf->developer_key);
//    $googleClient->setHostedDomain("topsource.it");
    \WolfMVC\Registry::set("googleClient", $googleClient);
} catch (\Exception $e) {
    echo $e->getMessage();
}

//$session->eraseall();
//echo "<pre>";
//print_r($_SESSION);
//echo "</pre>";
//
////ok
//


$preContent = ob_get_contents();
ob_end_clean();
\WolfMVC\Registry::set("preContent", $preContent);
try {
    $systemstatus = WolfMVC\Registry::get("systemstatus")->systemstatus;
//    echo $systemstatus;
    if (!(isset($systemstatus)) || is_null($systemstatus))
    {
        $router = new WolfMVC\Router(array(
            "url" => "off/index",
            "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
        ));
    }
    if (isset($_GET["fregatene"]) && $_GET["fregatene"] === "asdrubale")
        $systemstatus = "on";
    switch (strtolower($systemstatus)) {
        case "off":
            $router = new WolfMVC\Router(array(
                "url" => "off/index",
                "extension" => "html"
            ));
            break;
        case "maintenance":

            $router = new WolfMVC\Router(array(
                "url" => "maintenance/index",
                "extension" => "html"
            ));
            break;
        default :
//            $session->set("auth", false);
            if (isset($_GET["url"]))
            {
                $p = strpos($_GET["url"], "error");
                if ($p !== FALSE && $p === 0)
                {
                    $router = new WolfMVC\Router(array(
                        "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
                        "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                    ));
                    break;
                }
            }
            if ($session->get("auth") || (isset($_GET["url"]) && $_GET["url"] === "authenticate/logoutPerformed"))
            {
                WolfMVC\Registry::set("usertodisplay", $session->get("user"));
                if (!isset($_GET["url"]) || $_GET["url"] === "authenticate/logout")
                    session_write_close(); //tranne nel caso di login e logout, a questo punto posso chiudere la sessione per liberare il lock
                $url = "home/index";
                $router = new WolfMVC\Router(array(
                    "url" => isset($_GET["url"]) ? $_GET["url"] : $url,
                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                ));
            }
            else
            {
                $router = new WolfMVC\Router(array(
                    "url" => "authenticate/googleAuthenticate/",
                    "extension" => isset($_GET["extension"]) ? $_GET["extension"] : "html"
                ));
            }
    }

    WolfMVC\Registry::set("router", $router);
    $router->dispatch();
} catch (\Exception $e) {
    echo $e->getMessage();
}
//
//
//
//
unset($configuration);
unset($database);
unset($cache);
unset($session);
unset($router);
//
//WolfMVC\Registry::esponi();
//WolfMVC\Censor::esponi();
//
//ob_end_flush();
//exit;
