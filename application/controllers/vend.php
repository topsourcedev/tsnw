<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

use WolfMVC\Controller as Controller;

class Vend extends Controller {

    protected $_conf;

    public function __construct($options = array()) {
        parent::__construct($options);
    }

    /**
     * @protected
     */
    public function script_including() {

        /*
         * temporaneamente appoggio qui l'autorizzazione sul controller
         */
        $session = \WolfMVC\Registry::get("session");
        $user = $session->get("user");
        if ($user !== "Alberto Brudaglio")
        {
            header("Location: " . SITE_PATH);
        }

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/data.js\"></script>";

        $view = $this->getLayoutView();
        $view->set("moduleName", "PANNELLO DI AMMINISTRAZIONE");
    }

    public function index() {
        $view = $this->getActionView();
        $view->set("action", array(
            "Riepilogo pagine" => SITE_PATH . $this->nameofthiscontroller() . "/riepilogo_pagine",
            "Autorizzazione pagine" => SITE_PATH . $this->nameofthiscontroller() . "/autorizzazione_pagine",
            "Gestione utenti" => SITE_PATH . $this->nameofthiscontroller() . "/gestione_utenti"
        ));
    }

    public function riepilogo_pagine() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("AMMINISTRAZIONE" => "sonosuperfigo", "Riepilogo pagine" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function gestione_utenti() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("AMMINISTRAZIONE" => "sonosuperfigo", "Gestione utenti" => "last")));
        $view = $this->getActionView();
        $view->set("first", '$first');
        $view->set("root", '$root');
        $view->set("index", '$index');
    }

    public function ws___riepilogo_pagine() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getElencoPagine" => SITE_PATH . $this->nameofthiscontroller() . "/getElencoPagine.ws",
                "getUtenti" => SITE_PATH . $this->nameofthiscontroller() . "/getUtenti.ws"
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___gestione_utenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $config = array(
            "services" => array(
                "getUtenti" => SITE_PATH . $this->nameofthiscontroller() . "/getUtenti.ws",
                "getUtentiVT" => SITE_PATH . $this->nameofthiscontroller() . "/getUtentiVT.ws"
            ),
            "resuri" => array (
                "img" => array (
                    "plus" => SITE_PATH . "img/plus.png"
                    )
            )
        );

        echo json_encode($config);
        return;
    }

    public function ws___getElencoPagine() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $dir = APP_PATH . "/application/controllers";
        $controllers = array();
        if (is_dir($dir))
        {
            $files = scandir($dir);
            foreach ($files as $file) {
                $split = explode(".", $file);
                if ($split[count($split) - 1] === "php" && (array_pop($split) !== NULL))
                {
//
                    $ctrlname = join(".", $split);
                    $ctrlclass = ucfirst($ctrlname);

                    if (class_exists($ctrlclass))
                    {
                        $meth = array();
                        $methods = get_class_methods($ctrlclass);
                        $vars = get_class_vars($ctrlclass);
                        $dirview = APP_PATH . "/{$vars["_defaultPath"]}/{$ctrlname}/";
                        if (is_dir($dirview))
                        {
                            $viewfiles = scandir($dirview);
                        }
                        foreach ($methods as $k => $m) {
                            $index = array_search($m . ".html", $viewfiles);
                            if ($index !== FALSE)
                            {
                                $v = $viewfiles[$index];
                            }
                            else
                            {
                                $v = "";
                            }
                            array_push($meth, array("method" => $m, "view" => $v));
                        }
                    }
                    $controllers[$ctrlname] = $meth;
                }
            }
        }
        echo json_encode($controllers);
        return;
    }

    public function ws___getUtenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $db = \WolfMVC\Registry::get("database_tsnw");
        $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
        $sql = "SELECT id,user_name,first_name,last_name,active FROM users";
        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }
        $result = $link->query($sql);
        if ($result)
        {
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));
            return;
        }
        else
        {
            echo json_encode("An error occurred retrieving users: " . $link->error);
            return;
        }
    }

    public function ws___getUtentiVT() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $client = \WolfMVC\Registry::get("VTWS");
        $records = $client->doQuery("SELECT user_name,email1,id,first_name,last_name, roleid, status FROM USERS;");
//        print_r($records);
        echo json_encode($records);
        return;
    }

    public function autenticazione_pagine() {
        $view = $this->getActionView();
    }

    public function supervisione() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "last")));
        $view = $this->getActionView();

        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("action_1", "Assegnazione progetti");
        $view->set("path_action_1", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_assegnare/1");
        $view->set("action_3", "Riassegnazione progetti non avviati");
        $view->set("path_action_3", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_riassegnare/1");
        $view->set("action_2", "Richieste di conferma");
        $view->set("path_action_2", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_confermare/1");
//        $view->set("gest_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/gest");
//        $view->set("amm_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
//        $view->set("bankinsert_path", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/amm");
    }

    public function gestione() {
        $view = $this->getActionView();
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "last")));
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("action_1", "Avvio progetti");
        $view->set("path_action_1", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/progetti_da_avviare/1");
        $view->set("action_2", "I miei progetti");
        $view->set("path_action_2", SITE_PATH . \WolfMVC\Registry::get("router")->getController() . "/i_miei_progetti/1");
    }

    /**
     * @before script_including
     */
    public function progetti_da_assegnare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da assegnare" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaassegnare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_assegnare.css\">";


        return;
    }

    /**
     * @before script_including
     */
    public function i_miei_progetti() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "I miei progetti" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("root", '$root');
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/imieiprogetti.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/i_miei_progetti.css\">";


        return;
    }

    public function ws___i_miei_progetti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("project", "##projectstatus## = 'in progress'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Assegnato a", "Situazione progetto", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___progetti_da_assegnare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("project", "##projectstatus## = 'prospecting'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___progetti_da_confermare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("projectcf", "##cf_693## = '1'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function progetti_da_riassegnare() {
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());

        $struct = self::getModelStructure("progetti");
        $smm = new WolfMVC\Smm();
        $smm->setStructure($struct);
        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
//        echo "<pre>";
//        print_r($data);
//        echo "</pre>";
        return;
    }

    public function progetti_da_confermare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da confermare" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaconfermare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_confermare.css\">";


        return;
    }

    public function assegnazione() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da assegnare" => "progetti/progetti_da_assegnare", "Assegnazione" => "last")));
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/assegnazioneprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/assegnazione.css\">";
    }

    public function assegnazione2() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();

        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '{$projectid}'");
        $smm = new WolfMVC\Smm();
        $smm->setStructure($struct);
        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        $structPrType = self::getModelStructure("tipoprogetto");

        $smmPrType = new WolfMVC\Smm();
        $smmPrType->setStructure($structPrType);
        try {
            $prType = $smmPrType->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        $structCons = self::getModelStructure("consulenti");

        $smmCons = new WolfMVC\Smm();
        $smmCons->setStructure($structCons);
        try {
            $consulenti = $smmCons->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }

        echo "<!---<pre>";
        print_r($data);
        echo "</pre>--->";
        $dettagliProgetto = new WolfMVC\Template\Component\Prospectus();
        $dettagliProgetto->setComponents(array(
            "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Assegnato a"
        ));
        $dettagliProgetto->setData($data["data"][0]);
        $dataAperturaProgetto = $data["data"][0]["Data apertura progetto"];
        $prId = $data["data"][0]["Pk_project"];
        $dettagliProgetto->setArrangement(array(
            "{{Numero progetto:__label__}}&nbsp;&nbsp;{{Numero progetto}}&nbsp;&nbsp;&nbsp;{{Nome progetto:__label__}}&nbsp;&nbsp;{{Nome progetto}}",
            "Tipologia progetto:&nbsp;&nbsp;{{Tipo progetto}}",
            "Data apertura prevista:&nbsp;&nbsp;{{Data apertura progetto}}",
            "{{Assegnato a:__label__}}&nbsp;&nbsp;{{Assegnato a}}"
        ));
        $view->set("dettagliProgetto", $dettagliProgetto->make(""));
        $dettagliCliente = new WolfMVC\Template\Component\Prospectus();
        $dettagliCliente->setComponents(array(
            "Numero cliente", "Azienda cliente", "Telefono cliente", "Ev.le altro telefono", "Email",
            "Fax", "Dipendenti", "Partita Iva", "Vendita gestita da", "Contatto gestito da", "Analista"
        ));
        $dettagliCliente->setData($data["data"][0]);

        $dettagliCliente->setArrangement(array(
            "{{Numero cliente:__label__}}&nbsp;&nbsp;{{Numero cliente}}&nbsp;&nbsp;&nbsp;{{Azienda cliente:__label__}}&nbsp;&nbsp;{{Azienda cliente}}",
            "{{Telefono cliente:__label__}}&nbsp;&nbsp;{{Telefono cliente}}&nbsp;&nbsp;&nbsp;{{Ev.le altro telefono:__label__}}&nbsp;&nbsp;{{Ev.le altro telefono}}",
            "{{Email:__label__}}&nbsp;&nbsp;{{Email}}",
            "{{Fax:__label__}}&nbsp;&nbsp;{{Fax}}",
            "{{Dipendenti:__label__}}&nbsp;&nbsp;{{Dipendenti}}&nbsp;&nbsp;&nbsp;{{Partita Iva:__label__}}&nbsp;&nbsp;{{Partita Iva}}",
            "Analista:&nbsp;&nbsp;{{Analista}}",
            "Venditore:&nbsp;&nbsp;{{Vendita gestita da}}"
        ));
        $view->set("dettagliCliente", $dettagliCliente->make(""));

        $dettagliContattoData = array();
        $dettagliContattoDataTmp = array();
        $dettagliContattoDataTmp['Contatto'] = explode(",", $data["data"][0]["Contatto"]);
        $dettagliContattoDataTmp['Tel'] = explode(",", $data["data"][0]["tel"]);
        $dettagliContattoDataTmp['Cell'] = explode(",", $data["data"][0]["cell"]);
        $dettagliContattoOut = "";
        foreach ($dettagliContattoDataTmp["Contatto"] as $k => $v) {
            $dettagliContattoData[$k] = array();
            $txt = $dettagliContattoDataTmp['Contatto'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Contatto"] = $txt;
            $txt = $dettagliContattoDataTmp['Tel'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Tel"] = $txt;
            $txt = $dettagliContattoDataTmp['Cell'][$k];
            $txt = explode(": ", $txt);
            $txt = str_ireplace("}", "", $txt[1]);
            $dettagliContattoData[$k]["Cell"] = $txt;
            $dettagliContatto = new WolfMVC\Template\Component\Prospectus();
            $dettagliContatto->setComponents(array(
                "Contatto", "Tel", "Cell"
            ));
            $dettagliContatto->setData($dettagliContattoData[$k]);
            $dettagliContatto->setArrangement(array(
                "{{Contatto:__label__}}&nbsp;&nbsp;{{Contatto}}",
                "{{Tel:__label__}}&nbsp;&nbsp;{{Tel}}",
                "{{Cell:__label__}}&nbsp;&nbsp;{{Cell}}"
            ));
            $dettagliContattoOut .= $dettagliContatto->make("");

            $form = "";
            $form .= "<form action=\"" . SITE_PATH . $this->nameofthiscontroller() . "/assegna\" method=\"POST\">";
            $form .= "<input type=\"hidden\" name=\"projectid\" value=\"{$prId}\">";

            $form .= "<label for=\"nomeProgetto\">Nuovo nome progetto:</label>&nbsp;&nbsp;"
                    . "<input size=\"50\" type=\"text\" name=\"nomeProgetto\" id=\"nomeProgetto\" "
                    . "value=\"{$data["data"][0]["Nome progetto"]}\"><br><br>";
            $form .= "<label for=\"tipoProgetto\">Nuova tipologia progetto:</label>&nbsp;&nbsp;"
                    . "<select name=\"tipoProgetto\" id=\"tipoProgetto\"> ";
            foreach ($prType["data"] as $k => $v) {
                $form .= "<option value=\"{$v['type']}\">{$v['type']}</option>";
            }
            $form .="</select><br><br>";
            $form .= "<label for=\"consulente\">Nuovo assegnatario:</label>&nbsp;&nbsp;"
                    . "<select name=\"consulente\" id=\"consulente\"> ";
            foreach ($consulenti["data"] as $consk => $cons) {
                $form .= "<option value=\"{$cons['Pk_consulenti']}\">{$cons['Consulente']}</option>";
            }
            $form .="</select><br><br>";
            $form .= "<label for=\"data\">Nuova data avvio:</label>&nbsp;&nbsp;"
                    . "<input type=\"date\" name=\"data\" id=\"data\" value=\"{$dataAperturaProgetto}\"><br><br>";
            $form .="<button type=\"submit\">Applica modifiche e assegna</button>";
            $form .= "</form>";


            $view->set("form", $form);
        }



        $view->set("dettagliContatto", $dettagliContattoOut);
    }

//    public function assegna() {
//        $lay = $this->getLayoutView();
//        $lay->set("breadCrumb",  $this->breadCrumb(array("PROGETTI"=>"progetti","Gestione" => "progetti/gestione", "Progetti da assegnare" => "progetti/progetti_da_assegnare", "Assegnazione" =>"last")));
//        $struct = self::getModelStructure("progetti");
//        $database = Registry::get("database_" . $struct->getDefaultDb());
//        $link = new \mysqli($database->getHost(), $database->getUsername(), $database->getPassword(), $database->getSchema());
//        $sql = "UPDATE vtiger_project " .
//                "SET " .
//                "projectname = '{$_POST['nomeProgetto']}', " .
//                "projecttype = '{$_POST['tipoProgetto']}', " .
//                "startdate = '{$_POST['data']}' " .
//                "WHERE projectid = {$_POST["projectid"]}";
//        $result = $link->query($sql);
//
//        $sql = "UPDATE vtiger_crmentity " .
//                "SET " .
//                "smownerid = '{$_POST['consulente']}' " .
//                "WHERE projectid = {$_POST["projectid"]}";
//        $result = $link->query($sql);
//        header("location: " . SITE_PATH . $this->nameofthiscontroller() . "/progetti_da_assegnare");
//    }

    /**
     * @before script_including
     */
    public function avvia() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da avviare" => "progetti/progetti_da_avviare", "Avvio progetto" => "last")));
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/avvioprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/avvia.css\">";
    }

    /**
     * @before script_including
     */
    public function avanzamento() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da assegnare" => "progetti/i_miei_progetti", "Avanzamento progetto" => "last")));
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/avanzamentoprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/avanzamento.css\">";
    }

    /**
     * @before script_including
     */
    public function conferma() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Supervisione" => "progetti/supervisione", "Progetti da confermare" => "progetti/progetti_da_confermare", "Conferma avanzamento" => "last")));
        $projectid = $this->_parameters[0];
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("index", '$index');
        $view->set("parent", '$parent');
        $view->set("root", '$root');
        $view->set("fieldIsString", "field.type === 'string'");
        $view->set("fieldIsDate", "field.type === 'date'");
        $view->set("fieldIsCurrency", "field.type === 'currency'");
        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/confermaprogetto.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/conferma.css\">";
    }

    public function ws___accountToSo() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $accountid = $this->_parameters[0];
        $sql = "SELECT " .
                "salesorderid as 'val', concat(salesorder_no, ' - ', subject ) as 'nome', " .
                "if (sostatus = 'Approved', '1', '0') as 'approved' " .
                "FROM " .
                "vtiger_salesorder " .
                "where accountid = '{$accountid}'";
        try {
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
                return;
            }
            else
            {
                if ($result = $link->query($sql))
                {
                    $data = array();
                    $fields = $result->fetch_fields();
                    $data["fields"] = array();
                    foreach ($fields as $key => $f) {
                        array_push($data["fields"], $f->name);
                    }
                    $data["data"] = $result->fetch_all(MYSQLI_ASSOC);
                }
                else
                {
                    echo "Failed to make query: (" . $link->error . ") ";
                }
            }
        } catch (\Exception $e) {
            print_r($e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        echo (json_encode($jsdata));
    }

    public function ws___consulenti() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $sql = "SELECT a.id as val, " .
                "CONCAT_WS (' ',first_name,last_name) as nome, " .
//                "b.roleid, c.rolename ," .
                "IF (roleid = 'H12', '1', '0') as 'resp' " .
                "FROM vtiger_users a " .
                "LEFT join vtiger_user2role b on (a.id = b.userid) " .
                "LEFT JOIN vtiger_role c USING (roleid) " .
                "WHERE roleid IN ('H12','H13')";
        try {
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo "Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error;
                return;
            }
            else
            {
                if ($result = $link->query($sql))
                {
                    $data = array();
                    $fields = $result->fetch_fields();
                    $data["fields"] = array();
                    foreach ($fields as $key => $f) {
                        array_push($data["fields"], $f->name);
                    }
                    $data["data"] = $result->fetch_all(MYSQLI_ASSOC);
                }
                else
                {
                    echo "Failed to make query: (" . $link->error . ") ";
                }
            }
        } catch (\Exception $e) {
            print_r($e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        echo (json_encode($jsdata));
    }

    public function ws___assegnazione() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avanzamento() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avanzamentoMoreInfo() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];


        $db = \WolfMVC\Registry::get("database_vtiger");
        $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());




        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');


        if ($link->connect_errno)
        {
            echo json_encode("Error occurred in db connection!");
            return;
        }

        //recupero info su scadenze
        $sql = "SELECT * "
                . "FROM external_project_milestone "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["esecuzione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su fatture
        $sql = "SELECT * "
                . "FROM external_project_invoices "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["fatturazione"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su incasso
        $sql = "SELECT * "
                . "FROM external_project_collections "
                . "WHERE projectid = '{$projectid}'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["incasso"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }
        //recupero info su attivita
        $sql = "SELECT projecttaskid, projecttaskname, description, projecttaskprogress "
                . "FROM vtiger_projecttask LEFT JOIN vtiger_crmentity ON (projecttaskid = crmid) "
                . "WHERE projectid = '{$projectid}' AND deleted = '0'";
        $result = $link->query($sql);
        if ($result)
        {
            $data["attivita"] = $result->fetch_all(MYSQLI_ASSOC);
        }
        else
        {
            echo json_encode("Error occured in querying db!");
            return;
        }


        echo json_encode($data);
        return;

        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___avvia() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un progetto", 0, NULL);
        }
        $projectid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti_ext");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
        $struct = self::getModelStructure("progetti_so");
        $struct->addFilterToTable("project", "##projectid## = '$projectid'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
//        $jsdata = array();
        $labels = $data["fields"]; //array("Pk_project", "Numero progetto", "Nome progetto", "Tipo progetto", "Data apertura progetto", "Stato progetto", "Assegnato a", "Azienda cliente", "Telefono cliente", "tel", "cell", "Contatto");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
    }

    public function ws___contatti() {
        if (!isset($this->_parameters[0]))
        {
            throw new \Exception("Devi selezionare un account", 0, NULL);
        }
        $accountid = $this->_parameters[0];

        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $client = \WolfMVC\Registry::get("VTWS");
//        print_r($client);
        $query = "SELECT * FROM Contacts WHERE account_id = '11x{$accountid}'";
        $records = $client->doQuery($query);
        if ($records)
        {
            $columns = $client->getResultColumns($records);
//            foreach ($records as $record) {
//            echo "<pre>";
//            print_r($records);
//            echo "</pre>";
//            }
        }
        echo (json_encode($records));
    }

    public function ws___salvaEsecuzioneAvvio() { // salva esecuzione avvio progetto
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["esecuzione"]))
        {
            $client = \WolfMVC\Registry::get("VTWS");
            if (!$client)
            {
                echo json_encode($client);
                return;
            }
            $data["esecuzione"] = json_decode($data["esecuzione"]);
            $sql = "INSERT INTO external_project_milestone "
                    . "(date,label,status,projectid,flowstatus,isactual,actualdate,actuallabel,create_date) "
                    . "VALUES";

            $esecuzioneraw = $data["esecuzione"];
            $esecuzione = array();
            $rows = array();
            $projectid = $this->anti_injection($esecuzioneraw->projectid);
//            echo $projectid;
            for ($i = 0; $i < (int) $esecuzioneraw->numrows; $i++) {
                if (!isset($esecuzioneraw->{$i}))
                    break;
                $esecuzione[$i] = $esecuzioneraw->{$i};
                if ($i == 0 && $esecuzione[$i]->actualDate !== "")
                {
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','0','{$projectid}','1','0','{$this->anti_injection($esecuzione[$i]->actualDate)}','',now())");
                }
                else
                {
                    array_push($rows, " ('{$this->anti_injection($esecuzione[$i]->budgetDate)}','{$this->anti_injection($esecuzione[$i]->label)}','0','{$projectid}','1','0','','',now())");
                }
            }

            $project = $client->doRetrieve("31x" . $projectid);
            $project["cf_693"] = '1';
            $update = $client->doUpdate($project);
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");

            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            if ($update && $link->query($sql))
            {
                echo json_encode("Piano esecuzione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano esecuzione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaFatturazioneAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["fatturazione"]))
        {
            $data["fatturazione"] = json_decode($data["fatturazione"]);
            $sql = "INSERT INTO external_project_invoices "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, create_date, isActual, actualamount) "
                    . "VALUES";

            $fatturazioneraw = $data["fatturazione"];
            if ((int) $fatturazioneraw->numrows === 0)
            {
                echo json_encode("Piano fatturazione (vuoto) correttamente inserito.");
                return;
            }
            $fatturazione = array();
            $rows = array();
            $projectid = $this->anti_injection($fatturazioneraw->projectid);
            $salesorderid = $this->anti_injection($fatturazioneraw->salesorderid);
//            echo $projectid;
            for ($i = 0; $i < (int) $fatturazioneraw->numrows; $i++) {
                if (!isset($fatturazioneraw->{$i}))
                    break;
                $fatturazione[$i] = $fatturazioneraw->{$i};
                array_push($rows, " ('{$this->anti_injection($fatturazione[$i]->date)}','{$this->anti_injection($fatturazione[$i]->amount)}','{$salesorderid}','0','1','{$projectid}',now(),'0','')");
            }
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            if ($link->query($sql))
            {
                echo json_encode("Piano fatturazione correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano fatturazione non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaIncassoAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["incasso"]))
        {
            $data["incasso"] = json_decode($data["incasso"]);
            $sql = "INSERT INTO external_project_collections "
                    . "(date, amount, salesorderid, status, flowstatus, projectid, type, create_date, isactual, actualdate, actualamount, actualtype) "
                    . "VALUES";

            $incassoraw = $data["incasso"];
            if ((int) $incassoraw->numrows === 0)
            {
                echo json_encode("Piano incasso (vuoto) correttamente inserito.");
                return;
            }

            $incasso = array();
            $rows = array();
            $projectid = $this->anti_injection($incassoraw->projectid);
            $salesorderid = $this->anti_injection($incassoraw->salesorderid);

            for ($i = 0; $i < (int) $incassoraw->numrows; $i++) {
                if (!isset($incassoraw->{$i}))
                    break;
                $incasso[$i] = $incassoraw->{$i};
                array_push($rows, " ('{$this->anti_injection($incasso[$i]->date)}','{$this->anti_injection($incasso[$i]->amount)}','{$salesorderid}','0','1','{$projectid}','{$this->anti_injection($incasso[$i]->type)}',now(),'0','','','')");
            }
            $sql .= join(", ", $rows);
            $db = \WolfMVC\Registry::get("database_vtiger");
            $link = new \mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                echo json_encode("Error occurred in db connection!");
                return;
            }
            if ($link->query($sql))
            {
                echo json_encode("Piano incasso correttamente inserito.");
                return;
            }
            else
            {
                echo json_encode("Il piano incasso non &eacute; stato correttamente inserito per via di un errore: " . $link->error);
                return;
            }
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaAssegnazione() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_REQUEST;

        if (isset($data["data"]))
        {
            $data = json_decode($data["data"]);
            $projectid = $data->projectid;
            $assegna = $data->assegna;
            $data = $data->info;
            $client = \WolfMVC\Registry::get("VTWS");

            $proj = $client->doRetrieve("31x" . $projectid);
            $proj = array_merge($proj, (array) $data);
            if ($assegna)
            {
                $proj["projectstatus"] = "initiated";
            }
            if ($client->doUpdate($proj))
            {
                echo json_encode("Update riuscito.");

                return;
            }
            else
            {
                echo json_encode("Update Failed");
                return;
            }

            return;
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvaAttivitaAvvio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $data = $_POST;

        if (isset($data["attivita"]))
        {
            $data["attivita"] = json_decode($data["attivita"]);
            $attivitaraw = $data["attivita"];
            $attivita = array();
            $rows = array();
            $projectid = $attivitaraw->projectid;
//            echo $projectid;

            for ($i = 0; $i < (int) $attivitaraw->numrows; $i++) {
                if (!isset($attivitaraw->{$i}))
                    break;
                $attivita[$i] = $attivitaraw->{$i};
            }
            $countsuccess = 0;
            $client = \WolfMVC\Registry::get("VTWS");
            $session = WolfMVC\Registry::get("session");
            $vtiger_logged_user_id = $session->get("vtiger_logged_user_id");
            foreach ($attivita as $attk => $att) {
                if ($record = $client->doCreate("ProjectTask", array(
                    "projecttaskname" => $att->name,
                    "projectid" => "31x" . $projectid,
                    "assigned_user_id" => "19x" . $vtiger_logged_user_id,
                    "modifiedby" => "19x" . $vtiger_logged_user_id,
                    "description" => $att->description
                        )))
                    $countsuccess++;
                else
                {
                    ob_start();
                    echo "Create {$countsuccess} attivit&agrave; su {$attivitaraw->numrows}. Si &eacute; verificato un errore";
                    print_r($record);
                    $ret = json_encode(ob_get_contents());
                    ob_end_clean();
                    echo $ret;
                    return;
                }
            }
            echo json_encode("Create {$countsuccess} attivit&agrave; su {$attivitaraw->numrows}.");
            return;
        }
        else
        {
            echo json_encode("Failed");
            return;
        }
    }

    public function ws___salvataggio() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
//        $view = $this->getActionView();
        header('Content-type: application/json');
        $data = $_POST;
        echo "Ho ricevuto i seguenti dati\n";
        print_r($_REQUEST);
    }

    public function progetti_da_avviare() {
        $lay = $this->getLayoutView();
        $lay->set("breadCrumb", $this->breadCrumb(array("PROGETTI" => "progetti", "Gestione" => "progetti/gestione", "Progetti da avviare" => "last")));
        $view = $this->getActionView();
        $view->set("action_0", "Indietro");
        $view->set("path_action_0", $this->getBackTrack());
        $view->set("angular_dollar_index", '$index');
        $view->set("expr_link", 'ListoneCtrl.avvia(datum["Pk_project"])');

        $this->_system_js_including .= "<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/angularjs/1.2.25/angular.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/progetti/progettidaavviare.js\"></script>";
        $this->_system_js_including .="<link rel=\"stylesheet\" href=\"" . SITE_PATH . "css/progetti/progetti_da_avviare.css\">";
        return;
    }

    public function ws___progetti_da_avviare() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $struct = self::getModelStructure("progetti");
        $struct->addFilterToTable("project", "##projectstatus## = 'initiated'");
        $smm = new WolfMVC\Smm();

        $smm->setStructure($struct);

        try {
            $data = $smm->selectAll();
        } catch (\Exception $e) {
            print_r(e);
        }
        foreach ($data["data"] as $k => $v) {
            foreach ($v as $i => $s) {
                $data["data"][$k][$i] = addslashes(utf8_encode($s));
            }
        }
        $jsdata = array();

        $labels = array("Pk_project", "Numero progetto", "Nome progetto", "Situazione progetto", "Tipo progetto", "Data apertura progetto", "Azienda cliente");
        foreach ($data["data"] as $k => $v) {
//            if ($k > 25)
//                break;
            $jsdata[$k] = array();
            foreach ($labels as $i => $l) {
                $jsdata[$k][$l] = $v[$l];
            }
//            $jsdata[$k]["A"] = $k;
        }
//        $jsdata[count($jsdata)] = array(count($data["data"]));
        echo (json_encode($jsdata));
//        echo json_encode($jsdata);
    }

    public function ws___describe() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);

//            $view = $this->getActionView();
        header('Content-type: application/json');
        $ret = array();
        $ret[0] = "No WS available at such address";
        $ret['RequestAccept'] = $this->parseAcceptHeader();
        echo json_encode($ret);
        exit;
//            $view->set("data", json_encode("No WS available at such address"));
    }

    public function ws___data() {
        $this->usePageComp("0.1");
        parent::ws___data();
    }

    public static function getModelStructure($modelname) {
        switch ($modelname) {
            case 'tipoprogetto':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_projecttype", "ptype");
                $struct->addField("projecttype", "ptype", "type");
                break;
            case 'consulenti':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_users", "consulenti");
                $struct->addTable("vtiger_user2role", "consulenti_ruoli");
                $struct->setRelation("consulenti", "consulenti_ruoli", "!", "id", "userid");
                $struct->addField("first_name", "consulenti", "nome");
                $struct->addField("last_name", "consulenti", "cognome");
                $struct->addField("roleid", "consulenti_ruoli", "ruolo");
                $struct->addFilterToField("ruolo", "##FIELD## = 'H4'");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("nome", "cognome"), "Consulente");
                break;
            case 'progetti':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
                        ->addTable("vtiger_projectcf", "projectcf")
                        ->addTable("vtiger_crmentity", "project_ent")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid")
                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));
                $struct->addField("projectname", "project", "Nome progetto");
                $struct->addField("projecttype", "project", "Tipo progetto");
                $struct->addField("startdate", "project", "Data apertura progetto");
                $struct->addField("projectstatus", "project", "Stato progetto");
                $struct->addField("project_no", "project", "Numero progetto");
                $struct->addField("smownerid", "project_ent", "Id assegnato a");
                $struct->addField("cf_693", "projectcf", "Situazione progetto");
                $struct->setDbDrivenPicklist("Tipo progetto", self::getModelStructure("tipoprogetto"), "type", "type");
                $struct->addTable("vtiger_users", "consulenti")->setRelation("project_ent", "consulenti", "*", "smownerid", "id");
                $struct->addField("first_name", "consulenti", "Consulente nome")->addField("last_name", "consulenti", "Consulente cognome");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Consulente nome", "Consulente cognome"), "Assegnato a");
                $struct->addTable("vtiger_account", "account")->addTable("vtiger_accountscf", "accountcf")->addTable("vtiger_crmentity", "account_ent");
                $struct->setRelation("account", "accountcf", "!", "accountid", "accountid")->setRelation("account", "account_ent", "!", "accountid", "crmid");
                $struct->setEntity("ACCOUNT", array("account", "accountcf", "account_ent"));
                $struct->setRelation("project", "account", "*", "linktoaccountscontacts", "accountid");
                $struct->addField("accountname", "account", "Azienda cliente")->addField("phone", "account", "Telefono cliente");

                $struct->addTable("vtiger_contactdetails", "contatti")->setRelation("account", "contatti", "+", "accountid", "accountid");
                $struct->addTable("vtiger_contactscf", "contatticf")->setRelation("contatti", "contatticf", "!", "contactid", "contactid");
                $struct->addTable("vtiger_crmentity", "contatti_ent")->setRelation("contatti", "contatti_ent", "!", "contactid", "crmid");
                $struct->setEntity("CONTATTI", array("contatti", "contatticf", "contatti_ent"));

                $struct->addField("firstname", "contatti", "Nome contatto")->addField("lastname", "contatti", "Cognome contatto")
                        ->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Nome contatto", "Cognome contatto"), "Contatto", true);
                $struct->addField("phone", "contatti", "tel")->addField("mobile", "contatti", "cell");
                $struct->addFilterToTable("contatti_ent", "##deleted## = '0'");
                $struct->addFilterToTable("project_ent", "##deleted## = '0'");
                $struct->addFilterToTable("account_ent", "##deleted## = '0'");
                $struct->setFieldsEditable(array(
                    "Nome progetto", "Tipo progetto", "Id assegnato a"
                ));

                break;
            case 'progetti_ext':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
                        ->addTable("vtiger_projectcf", "projectcf")
                        ->addTable("vtiger_crmentity", "project_ent")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid")
                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));

                $struct->addField("projectname", "project", "Nome progetto");
                $struct->addField("projecttype", "project", "Tipo progetto");
                $struct->addField("startdate", "project", "Data apertura progetto");
                $struct->addField("projectstatus", "project", "Stato progetto");
                $struct->addField("project_no", "project", "Numero progetto");
                $struct->addField("smownerid", "project_ent", "Id assegnato a");
                $struct->addField("description", "project_ent", "Descrizione");
                $struct->setDbDrivenPicklist("Tipo progetto", self::getModelStructure("tipoprogetto"), "type", "type");
                $struct->addFilterToField("Tipo progetto", "##FIELD## = '--none--'")->addFilterToField("Tipo progetto", " OR ##FIELD## = 'Prospecting'");
                $struct->addTable("vtiger_users", "consulenti")->setRelation("project_ent", "consulenti", "*", "smownerid", "id");
                $struct->addField("first_name", "consulenti", "Consulente nome")->addField("last_name", "consulenti", "Consulente cognome");
                $struct->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Consulente nome", "Consulente cognome"), "Assegnato a");
                $struct->addTable("vtiger_account", "account")->addTable("vtiger_accountscf", "accountcf")->addTable("vtiger_crmentity", "account_ent");
                $struct->setRelation("account", "accountcf", "!", "accountid", "accountid")->setRelation("account", "account_ent", "!", "accountid", "crmid");
                $struct->setEntity("ACCOUNT", array("account", "accountcf", "account_ent"));
                $struct->setRelation("project", "account", "*", "linktoaccountscontacts", "accountid");
                $struct->addTable("vtiger_potential", "pot")
                        ->setRelation("account", "pot", "!", "accountid", "related_to");
                $struct->addTable("vtiger_potentialscf", "potcf")
                        ->setRelation("pot", "potcf", "!", "potentialid", "potentialid");
                $struct->addFilterToTable("pot", "##potentialname## = 'PROGETTO'");
                $struct->addField("cf_655", "potcf", "Analista");
                $struct->addField("accountname", "account", "Azienda cliente")->addField("phone", "account", "Telefono cliente");
                $struct->addField("account_no", "account", "Numero cliente")->addField("annualrevenue", "account", "Fatturato");
                $struct->addField("otherphone", "account", "Ev.le altro telefono")->addField("email1", "account", "Email");
                $struct->addField("fax", "account", "Fax")->addField("employees", "account", "Dipendenti");
                $struct->addField("cf_641", "accountcf", "Partita Iva")->addField("cf_643", "accountcf", "Vendita gestita da");
                $struct->addField("cf_644", "accountcf", "Contatto gestito da");

                $struct->addTable("vtiger_contactdetails", "contatti")->setRelation("account", "contatti", "+", "accountid", "accountid");
                $struct->addTable("vtiger_contactscf", "contatticf")->setRelation("contatti", "contatticf", "!", "contactid", "contactid");
                $struct->addTable("vtiger_crmentity", "contatti_ent")->setRelation("contatti", "contatti_ent", "!", "contactid", "crmid");
                $struct->setEntity("CONTATTI", array("contatti", "contatticf", "contatti_ent"));

                $struct->addField("firstname", "contatti", "Nome contatto")->addField("lastname", "contatti", "Cognome contatto")
                        ->addCalculatedField("CONCAT_WS(' ',##1##,##2##)", array("Nome contatto", "Cognome contatto"), "Contatto", true);
                $struct->addField("phone", "contatti", "tel")->addField("mobile", "contatti", "cell");
                $struct->addFilterToTable("contatti_ent", "##deleted## = '0'");
                $struct->addFilterToTable("project_ent", "##deleted## = '0'");
                $struct->addFilterToTable("account_ent", "##deleted## = '0'");
                $struct->setFieldsEditable(array(
                    "Nome progetto", "Tipo progetto", "Id assegnato a"
                ));

                break;
            case 'progetti_so':
                $struct = new \WolfMVC\Smm\Smmstructure();
                $struct->setDefaultDb("vtiger");
                $struct->addTable("vtiger_project", "project")
//                        ->addTable("vtiger_crmentity", "project_ent")
                        ->addTable("vtiger_projectcf", "projectcf")
//                        ->setRelation("project", "project_ent", "!", "projectid", "crmid")
                        ->setRelation("project", "projectcf", "!", "projectid", "projectid");
//                        ->setEntity("PROJECT", array("project", "projectcf", "project_ent"));

                $struct->addField("projectname", "project", "Nome progetto")->addField("cf_691", "projectcf", "Id SO");

                $struct->addTable("vtiger_salesorder", "so")
                        ->addTable("vtiger_salesordercf", "socf")
                        ->addTable("vtiger_crmentity", "so_ent")
                        ->addTable("vtiger_sobillads", "sostreet")
                        ->setRelation("projectcf", "so", "!", "cf_691", "salesorderid")
                        ->setRelation("so", "so_ent", "!", "salesorderid", "crmid")
                        ->setRelation("so", "socf", "!", "salesorderid", "salesorderid")
                        ->setEntity("SO", array("so", "socf", "so_ent"));
                $struct->setRelation("so", "sostreet", "!", "salesorderid", "sobilladdressid");
                $struct->addField("total", "so", "Totale progetto ii");
                $struct->addField("subtotal", "so", "Totale progetto ie")->addField("salesorder_no", "so", "Numero SO");
                $struct->addField("bill_city", "sostreet", "Citta")->addField("bill_code", "sostreet", "Cap");
                $struct->addField("bill_state", "sostreet", "Provincia")->addField("bill_street", "sostreet", "Indirizzo");
                $struct->addFilterToTable("so_ent", "##deleted## = '0'");

                break;
            default :
                return parent::getModelStructure($modelname);
        }
        return $struct;
    }

}
