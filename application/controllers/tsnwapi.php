<?php

use WolfMVC\Controller as Controller;

class Tsnwapi extends Controller {

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
        if (!in_array($user, array("Alberto Brudaglio", "Vincenzo Cervadoro")))
        {
            header("Location: " . SITE_PATH);
        }

        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/jquery.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/angular-resource.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ngbootstrap.min.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/ng-ui-bootstrap-tpls-0.2.0.js\"></script>";
        $this->_system_js_including .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/core/data.js\"></script>";

        $view = $this->getLayoutView();
        $view->set("moduleName", "API TSNW");
    }

    public function index() {
        
    }

    public function setup() {
        $view = $this->getLayoutView();
        $view->set("moduleName", "API TSNW: SETUP");
        $model = new Tsnwmodel(array("setup" => TRUE));
        $view = $this->getActionView();
        $view->set("out", $model->setup());
    }

    public function ws___json() {
        $this->setWillRenderActionView(false);
        $this->setWillRenderLayoutView(false);
        header('Content-type: application/json');
        $tsnw = new Tsnwserver();
        $tsnw->authorize(new Tsnwauth());


        //request collect
        $request = $tsnw->requestcollect($this->_parameters);
        $pub = new Tsnwpublisher();

        try {
            $processedRequest = $tsnw->requestanalyze()->getAnalyzedRequest();
            $op = $tsnw->actionperform();
            $pub->setContent($op);
        } catch (Tsnwexception $e) {
            $pub->setStatus($e->getCode());
            $pub->setContent($e->getMessage());
        } 
        catch (\Exception $e) {
            $pub->setStatus(500);
            $pub->setContent("An error occurred during request analysis. It's not possible to respond! ".$e->getMessage()."   ".$e->getTrace());
//            $pub->setContent("An error occurred during request analysis. It's not possible to respond!");
        }
//        echo json_encode($request->getRequest(),JSON_FORCE_OBJECT);
        echo json_encode($pub->publish(), JSON_FORCE_OBJECT);
    }

}
