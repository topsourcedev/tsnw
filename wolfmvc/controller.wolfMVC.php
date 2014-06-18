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

        protected function _getExceptionForImplementation($method) {
            //return new Exception\Implementation("{$method} method not implemented");
            return new Exception\Implementation(Registry::get("language")->sh("WolfMVC.Controller.Exception.Implementation", array($method)));
        }

        protected function _getExceptionForArgument() {
            return new Exception\Argument("Invalid argument");
        }

        public function nameofthiscontroller(){
            $router = \WolfMVC\Registry::get("router");
            return $router -> getController();
        }
        
        public function index() {
            echo Registry::get("language")->sh("WolfMVC.Controller.genericindexmethod");
        }

        public function render() {
            if (method_exists(get_class($this), "script_including")) {
                $this->script_including();
            }
            $defaultContentType = $this->getDefaultContentType();
            $results = null;
            $doAction = $this->getWillRenderActionView() && $this->getActionView();
            $doLayout = $this->getWillRenderLayoutView() && $this->getLayoutView();

            try {
                if ($doAction) {
                    $view = $this->getActionView();
                    $results = $view->render();
                }

                if ($doLayout) {
                    $view = $this->getLayoutView();
                    $view->set("system_js_including", $this->_system_js_including);
                    $view->set("template", $results);
                    $results = $view->render();

                    header("Content-type: {$defaultContentType}");
                    echo $results;
                }
                else if ($doAction) {
                    header("Content-type: {$defaultContentType}");
                    echo $results;

                    $this->setWillRenderLayoutView(false);
                    $this->setWillRenderActionView(false);
                }
            }
            catch (\Exception $e) {
                throw new View\Exception\Renderer("Invalid layout/template syntax");
            }
        }

        public function __destruct() {
            $this->render();
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

        public function parseAcceptHeader() {
            $hdr = $_SERVER['HTTP_ACCEPT'];
            $accept = array();
            foreach (preg_split('/\s*,\s*/', $hdr) as $i => $term) {
                $o = new \stdclass;
                $o->pos = $i;
                if (preg_match(",^(\S+)\s*;\s*(?:q|level)=([0-9\.]+),i", $term, $M)) {
                    $o->type = $M[1];
                    $o->q = (double) $M[2];
                }
                else {
                    $o->type = $term;
                    $o->q = 1;
                }
                $accept[] = $o;
            }
            usort($accept, function ($a, $b) {
                /* first tier: highest q factor wins */
                $diff = $b->q - $a->q;
                if ($diff > 0) {
                    $diff = 1;
                }
                else if ($diff < 0) {
                    $diff = -1;
                }
                else {
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

            if ($this->getWillRenderLayoutView()) {
                $defaultPath = $this->getDefaultPath();
                $defaultLayout = $this->getDefaultLayout();
                $defaultExtension = $this->getDefaultExtension();

                $view = new View(array(
                  "file" => APP_PATH . "/{$defaultPath}/{$defaultLayout}.{$defaultExtension}"
                ));

                $this->setLayoutView($view);
            }

            if ($this->getWillRenderLayoutView()) {
                $router = Registry::get("router");
                $controller = $router->getController();
                $action = $router->getAction();

                $view = new View(array(
                  "file" => APP_PATH . "/{$defaultPath}/{$controller}/{$action}.{$defaultExtension}"
                ));

                $this->setActionView($view);
            }
        }

    }

}
