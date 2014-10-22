<?php

require_once('../application/libraries/tsnwapi/tsnwauth.php');
require_once('../application/libraries/tsnwapi/tsnwmodel.php');
require_once('../application/libraries/tsnwapi/tsnwoperation.php');
require_once('../application/libraries/tsnwapi/tsnwanalyzer.php');
require_once('../application/libraries/tsnwapi/tsnwpublisher.php');
require_once('../application/libraries/tsnwapi/tsnwrequestcollect.php');

class Tsnwserver extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var Tsnwrequestcollect
     */
    protected $_request;

    /**
     * @readwrite
     * @var Tsnwanalyzer
     */
    protected $_analyzedrequest;

    public function __construct($options = array()) {
        parent::__construct($options);
    }


    
    public function authorize($tsnwauth) {
        if (!($tsnwauth instanceof Tsnwauth))
        {
            throw new \Exception("Invalid tsnwauth object", 0, null);
        }
        return true;
    }

    public function requestcollect($parameters) {
        $this->_request = new Tsnwrequestcollect($parameters);
        return $this->_request;
    }

    public function requestanalyze() {
        if (!isset($this->_request) || (!($this->_request instanceof Tsnwrequestcollect)))
        {
            throw new \Exception("Invalid tsnwrequestcollect object", 0, null);
        }
        $this->_analyzedrequest = new Tsnwanalyzer(array("request" => $this->_request));
        return $this->_analyzedrequest->analyze();
    }

}
?>

