<?php

use WolfMVC\Controller as Controller;
use WolfMVC\Smm as Smm;

class Test extends Controller {

    public function __construct($options = array()) {

        parent::__construct($options);
    }

    public function index() {
        require_once('../application/libraries/vtwsclib/vtiger/WSClient.php');
        $url = 'http://54.213.213.176/vtigertest/webservice.php';
        $vtigerAdminAccessKey = 'tNkzlCVdaphElMuj';
        $userName = "admin";
        $client = new Vtiger_WSClient($url);
        $login = $client->doLogin($userName, $vtigerAdminAccessKey);
        if (!$login)
            echo 'Login Failed';
        else
        {
            echo "<pre>";
            print_r($client);
            echo "</pre><br>";

            $query = "SELECT * FROM ProjectTask LIMIT 10;";
//            echo $query;
            try {
                $records = $client->doQuery($query);
            } catch (\Exception $e) {
                echo "errore";
                return;
            }
            if ($records)
            {
                $columns = $client->getResultColumns($records);
                echo "<pre>";
                print_r($records);
                echo "</pre><br>";
            }


            $modules = $client->doListTypes();
            foreach ($modules as $modulename => $moduleinfo) {
                echo "ModuleName: {$modulename}\n<BR>";
                $describe = $client->doDescribe($modulename);
                echo "<pre>";
                print_r($describe);
                echo "</pre>";
                $cancreate = $describe["createable"];
                $canupdate = $describe["updateable"];
                $candelete = $describe["deleteable"];
                $canread = $describe["retrieveable"];
                $fields = $describe["fields"];
            }
        }
    }

    public function google() {
        $view = $this->getActionView();
        $view->set("preContent",  \WolfMVC\Registry::get("preContent"));
    }

}
