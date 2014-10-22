<?php

class Tsnwanalyzer extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var Tsnwrequestcollect
     */
    protected $_request;
    
    public function __construct($options = array()) {
        parent::__construct($options);
    }
    
    public function analyze(){
        $model = new Tsnwmodel();
        $rels = array();
        $model->abstractrelations($model->getResources(),$rels);
        return $rels;
//        return array_merge($this->_request,$model->getResources());
//        return $model->getResources();
//        print_r($this->_request->getRequest());
        
        //identify target resource
        
        //verb
//        $verb = $th
        
    }
}
?>

