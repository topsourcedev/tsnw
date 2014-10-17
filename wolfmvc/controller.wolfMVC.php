<?php

namespace WolfMVC {

    use WolfMVC\Base as Base;
    use WolfMVC\View as View;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Template as Template;
    use WolfMVC\Controller\Exception as Exception;

    /**
     * Classe base di tutti i controller
     */
    class Controller extends Base {

        /**
         * @var array I parametri passati da url
         * @readwrite
         */
        protected $_parameters;

        /**
         * @readwrite
         */
        protected $_layoutView; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_actionView; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_willRenderLayoutView = true; //flag per render automatico

        /**
         * @readwrite
         */
        protected $_willRenderActionView = true; //flag per render automatico

        /**
         * @var string Il path relativo standard in cui cercare la view
         * @readwrite
         */
        protected $_defaultPath = "application/views";

        /**
         * @var string Il path relativo standard in cui cercare il layout
         * @readwrite
         */
        protected $_defaultLayout = "layouts/standard";

        /**
         * @readwrite
         */
        protected $_defaultExtension = "html";

        /**
         * @readwrite
         */
        protected $_defaultContentType = "text/html";

        /**
         * @readwrite
         */
        protected $_system_js_including = "";

        /**
         *
         * @readwrite
         */
        protected $_useBackTracking = false;

        /**
         *
         * @readwrite
         */
        protected $_usePageComponent = false;

        /**
         * @read
         * @var array
         */
        protected $_regOperations;

        protected function anti_injection($input) {
            $pulito = strip_tags(addslashes(trim($input)));
            $pulito = str_replace("'", "\'", $pulito);
            $pulito = str_replace('"', '\"', $pulito);
            $pulito = str_replace(';', '\;', $pulito);
            $pulito = str_replace('--', '\--', $pulito);
            $pulito = str_replace('+', '\+', $pulito);
            $pulito = str_replace('(', '\(', $pulito);
            $pulito = str_replace(')', '\)', $pulito);
            $pulito = str_replace('=', '\=', $pulito);
            $pulito = str_replace('>', '\>', $pulito);
            $pulito = str_replace('<', '\<', $pulito);
            return $pulito;
        }

        protected function breadCrumb($path) {
            $ret = "";
            foreach ($path as $k => $v) {
                if ($v === "last")
                {
                    $ret .= "<li class=\"active\">{$k}</li>\n";
                }
                else
                {
                    $ret .= "<li><a href=\"" . SITE_PATH . "{$v}\">{$k}</a></li>\n";
                }
            }
            return $ret;
        }

        protected function setBackTrack() {
            $session = \WolfMVC\Registry::get("session");
            $back = $session->get("backtrack");
            if ($back === NULL)
            {
                $back = array();
            }
            else
            {
                $last = $_SERVER['REQUEST_URI'];
                $search = array_search($last, $back);
                if ($search === FALSE)
                {
                    array_push($back, $last);
                }
                else
                {
                    $back = array_slice($back, 0, $search + 1);
                }
            }
            $session->set("backtrack", $back);
        }

        protected function getBackTrack() {
            $session = \WolfMVC\Registry::get("session");
            $back = $session->get("backtrack");
            if (!$back || empty($back))
            {
                return null;
            }
            if (count($back) < 2)
            {
                return null;
            }
            else
            {
                return $back[count($back) - 2];
            }
        }

        /*
         * PAGE COMPONENT
         * 
         */

//        protected function usePageComp($version = "0.1") {
//            $this->_usePageComponent = $version;
//            $this->_regOperations = array();
//            return $this;
//        }
//
//        //pars è un vettore di segnaposto
//        public function setPageOp($name, $mode, $pars = array()) {
//            if (!isset($this->_regOperations) || !(is_array($this->_regOperations)))
//            {
//                $this->_regOperations = array();
//            }
//            if (!isset($this->_regOperations["page"]) || !is_array($this->_regOperations["page"]))
//            {
//                $this->_regOperations["page"] = array();
//            }
//            if (!isset($this->_regOperations["page"]["byName"]) || !is_array($this->_regOperations["page"]["byName"]))
//            {
//                $this->_regOperations["page"]["byName"] = array();
//            }
//            if (!isset($this->_regOperations["page"]["byCode"]) || !is_array($this->_regOperations["page"]["byCode"]))
//            {
//                $this->_regOperations["page"]["byCode"] = array();
//            }
//            if (!is_string($mode))
//            {
//                throw new \Exception("Mode must be a string", 0, null);
//            }
//            if (!is_array($pars))
//            {
//                throw new \Exception("Pars must be an array", 0, null);
//            }
//            $newoperationcode = Registry::hashing(15);
//            $newop = new \stdClass();
//            $newop->operationCode = $newoperationcode;
//            $newop->mode = $mode;
//            $newop->pars = $pars;
//            $jsCode = "";
//            //analizzo operazione
//            switch ($mode) {
//                case 'locationreplace':
//                    $jsCode .= "function wmvc_regOperation_(pars) {\n" .
//                            "window.location.replace('###PAR1###');\n" .
//                            "}\n";
//                    break;
//                case 'locationhref':
//                    $jsCode .= "function wmvc_regOperation() {\n" .
//                            "window.location.href('###PAR1###');\n" .
//                            "}\n";
//                    break;
//            }
//            $newop->jsCode = $jsCode;
//
//            $this->_regOperations["page"]["byName"][$name] = $newop;
//            $this->_regOperations["page"]["byCode"][$newoperationcode] = $newop;
//            return $this;
//        }
//
        public function setDataOp($mode, $pars = array()) {
            if (!isset($this->_regOperations) || !(is_array($this->_regOperations)))
            {
                $this->_regOperations = array();
            }
            if (!isset($this->_regOperations["data"]) || !is_array($this->_regOperations["data"]))
            {
                $this->_regOperations["data"] = array();
            }
            if (!is_string($mode))
            {
                throw new \Exception("Mode must be a string", 0, null);
            }
            if (!is_array($pars))
            {
                throw new \Exception("Pars must be an array", 0, null);
            }
            $newoperationcode = Registry::hashing(15);
            $this->_regOperations["data"][$newoperationcode] = array($mode, $pars);
            return $this;
        }

        public function ws___data() {


//            $view = $this->getActionView();
            header('Content-type: application/json');
            if (!isset($this->_usePageComponent) || !($this->_usePageComponent))
            {
                echo json_encode("This controller doesn't allow pageComponent Service");
                exit;
            }
            $ret = array();
            $ret[0] = "No WS available at such address";
            $ret['RequestAccept'] = $this->parseAcceptHeader();
            echo json_encode("La componente " . $this->_parameters[0] . " richiede l'operazione " . $this->_parameters[1]);
            exit;
//            $view->set("data", json_encode("No WS available at such address"));
        }

        /*
         * 
         * 
         * 
         */

        protected function _getExceptionForImplementation($method) {
            //return new Exception\Implementation("{$method} method not implemented");
            return new Exception\Implementation(Registry::get("language")->sh("WolfMVC.Controller.Exception.Implementation", array($method)));
        }

        protected function _getExceptionForArgument() {
            return new Exception\Argument("Invalid argument");
        }

        public function nameofthiscontroller() {
            $router = \WolfMVC\Registry::get("router");
            return $router->getController();
        }

        public function nameofthisaction() {
            $router = \WolfMVC\Registry::get("router");
            return $router->getAction();
        }

        public function index() {
            echo Registry::get("language")->sh("WolfMVC.Controller.genericindexmethod");
        }

        public function render() {
            $layoutenvvars = array(
                "stdpagetitle" => "WolfMVC",
                "customlogo" => "",
                "user" => ""
            );
            $envvars = \WolfMVC\Registry::get("layoutenvvars");
            if (!(is_null($envvars)))
            {
                foreach ($layoutenvvars as $key => $var) {
                    if (isset($envvars->$key) && !(is_null($envvars->$key)))
                    {
                        $layoutenvvars[$key] = $envvars->$key;
                    }
                }
            }

            if (method_exists(get_class($this), "script_including"))
            {
                $this->script_including();
            }
            $defaultContentType = $this->getDefaultContentType();
            $results = null;
            $doAction = $this->getWillRenderActionView() && $this->getActionView();
            $doLayout = $this->getWillRenderLayoutView() && $this->getLayoutView();

            try {
                if ($doAction)
                {
                    $view = $this->getActionView();
                    $view->set("imgpath", "'/tsnwprerelease/public/img/'");

                    $results = $view->render();
                }

                if ($doLayout)
                {
                    $view = $this->getLayoutView();
                    $view->set("system_js_including", $this->_system_js_including);
                    $view->set("stdpagetitle", $layoutenvvars["stdpagetitle"]);
                    $view->set("customlogo", $layoutenvvars["customlogo"]);
                    $view->set("logoutpath", "'/tsnwprerelease/public/authenticate/logout'");
                    $view->set("imgpath", "/tsnwprerelease/public/img/");
                    $view->set("sitepath", SITE_PATH);
                    ob_start();
                    print_r($_SESSION);
                    $sess = ob_get_contents();
                    ob_end_clean();
                    $view->set("session", $sess);
                    //autoinclude css and js
                    $cont = $this->nameofthiscontroller();
                    $act = $this->nameofthisaction();
                    $css_autoinclude = array();
                    $dir = APP_PATH . '/public/css/autoinclude/' . $cont . "/" . $act;

//                    echo $dir;
                    if (is_dir($dir))
                    {
                        $files = scandir($dir);
//                        print_r($files);
                        foreach ($files as $file) {
                            if ($file !== "." && $file !== "..")
                            {
                                array_push($css_autoinclude, str_ireplace("/var/www", "", $dir) . "/" . $file);
                            }
                        }
                    }
                    if (count($css_autoinclude) > 0)
                    {
                        $view->set("css_autoinclude", $css_autoinclude);
                    }
                    $js_autoinclude = array();
                    $dir = APP_PATH . '/public/js/autoinclude/' . $cont . "/" . $act;

//                    echo $dir;
                    if (is_dir($dir))
                    {

                        $files = scandir($dir);
//                        print_r($files);
                        foreach ($files as $file) {
                            if ($file !== "." && $file !== "..")
                            {
                                array_push($js_autoinclude, str_ireplace("/var/www", "", $dir) . "/" . $file);
                            }
                        }
                    }
                    if (count($js_autoinclude) > 0)
                    {
                        $view->set("js_autoinclude", $js_autoinclude);
                    }
                    $homeIcon = $view->get("imgpath") . "home.png";
                    $view->set("homeIcon", $homeIcon);
                    $session = \WolfMVC\Registry::get("session");
                    if ($session->get("gLogged") && $session->get("auth"))
                    {
                        $view->set("gLogged", true);
                        $googleIcon = $view->get("imgpath") . "google.png";
                        $view->set("googleIcon", $googleIcon);
                    }
                    if ($session->get("vtiger_logged_user_id") && $session->get("auth"))
                    {
                        $view->set("vtiger_logged_user_id", $session->get("vtiger_logged_user_id"));
                        $vtigerIcon = $view->get("imgpath") . "vtiger.png";
                        $view->set("vtigerIcon", $vtigerIcon);
                    }
                    $usertodisplay = \WolfMVC\Registry::get("usertodisplay");
                    if ((isset($usertodisplay)) && $usertodisplay !== "" && (!(is_null($usertodisplay))))
                    {
                        $layoutenvvars["user"] = $usertodisplay;
                    }
                    if ($session->get("auth"))
                    {
                        $view->set("user", $layoutenvvars["user"]);
                    }
                    $view->set("template", $results);
                    $results = $view->render();

                    header("Content-type: {$defaultContentType}");
                    echo $results;
                }
                else if ($doAction)
                {
                    header("Content-type: {$defaultContentType}");
                    echo $results;

                    $this->setWillRenderLayoutView(false);
                    $this->setWillRenderActionView(false);
                }
            } catch (\Exception $e) {
                throw new View\Exception\Renderer("Invalid layout/template syntax");
            }
        }

        public function __destruct() {
            $this->render();
        }

        public function disablerender() {
            $this->setWillRenderActionView(false);
            $this->setWillRenderLayoutView(false);
        }

        /**
         * @before disablerender
         */
        public function ws___describe() {


//            $view = $this->getActionView();
            header('Content-type: application/json');
            $ret = array();
            $ret[0] = "No WS available at such address";
            $ret['RequestAccept'] = $this->parseAcceptHeader();
            echo json_encode($ret);
            exit;
//            $view->set("data", json_encode("No WS available at such address"));
        }

        public function parseAcceptHeader() {
            $hdr = $_SERVER['HTTP_ACCEPT'];
            $accept = array();
            foreach (preg_split('/\s*,\s*/', $hdr) as $i => $term) {
                $o = new \stdclass;
                $o->pos = $i;
                if (preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $term, $M))
                {
                    $o->type = $M[1];
                    $o->q = (double) $M[2];
                }
                else
                {
                    $o->type = $term;
                    $o->q = 1;
                }
                $accept[] = $o;
            }
            usort($accept, function ($a, $b) {
                /* first tier: highest q factor wins */
                $diff = $b->q - $a->q;
                if ($diff > 0)
                {
                    $diff = 1;
                }
                else if ($diff < 0)
                {
                    $diff = -1;
                }
                else
                {
                    /* tie-breaker: first listed item wins */
                    $diff = $a->pos - $b->pos;
                }
                return $diff;
            });
            $accept_data = array();
            foreach ($accept as $a) {
                $accept_data[$a->type] = $a->type;
            }
            return $accept_data;
        }

        public function __construct($options = array()) {
            parent::__construct($options);
//            $this->setBackTrack();
            $session = \WolfMVC\Registry::get("session");

            if ($this->getWillRenderLayoutView())
            {
                $defaultPath = $this->getDefaultPath();
                $defaultLayout = $this->getDefaultLayout();
                $defaultExtension = $this->getDefaultExtension();

                $view = new View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per il layout
                    "file" => APP_PATH . "/{$defaultPath}/{$defaultLayout}.{$defaultExtension}"
                ));

                $this->setLayoutView($view);
            }

            if ($this->getWillRenderLayoutView())
            {
                $router = Registry::get("router");
                $controller = $router->getController();
                $action = $router->getAction();



                $view = new View(array(//questo pezzo può essere replicato altrove per cambiare il file template usato per la vista
                    "file" => APP_PATH . "/{$defaultPath}/{$controller}/{$action}.{$defaultExtension}"
                ));
                $this->setActionView($view);
            }
        }

        //overwrite this in specific models
        public static function getModelStructure($modelname) {
            throw new \Exception("Unknown model " . $modelname, 0, null);
            return null;
        }

    }

}
