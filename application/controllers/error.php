<?php

use WolfMVC\Controller as Controller;

class Error extends Controller {

    public function script_including() {
        
    }

    public function index() {
        
    }

    public function noAuth() {
        
    }

    public function missingVT(){
        $view = $this->getActionView();
        $view->set("vtigerIcon",SITE_PATH."img/vtiger.png");
    }

}
