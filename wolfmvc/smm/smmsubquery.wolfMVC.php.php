<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmfield extends \WolfMVC\Base {

        /**
         * @readwrite
         * @var string 
         */
        protected $_name;

        /**
         * @readwrite
         * @var string 
         */
        protected $_alias;
        protected $_filters = array();
        /**
         * @readwrite
         * @var string
         */
        protected $_defaultmode;

        /**
         * @readwrite
         * @var string 
         */
        protected $_modes = array();

        public function __construct($options = array()) {
            parent::__construct($options);
            
            if (isset($this->_alias) && array_search("alias", $this->_modes) === FALSE) {
                array_push($this->_modes, "alias");
            }
            if (isset($this->_name) && array_search("tables", $this->_modes) === FALSE) {
                array_push($this->_modes, "name");
            }
            if (isset($this->_name) && isset($this->_alias) && array_search("name.alias", $this->_modes) === FALSE) {
                array_push($this->_modes, "name.alias");
            }
        }

        public function display($flag = "default") {
            switch ($flag) {
                case 'name':
                    return $this->_name;
                    break;
                case 'alias':
                    return $this->_alias;
                    break;
                case 'name.alias':
                    return $this->_name." as ".$this->_alias;
                    break;
                default:
                    if (array_search($this->_defaultmode, $this->_modes) !== FALSE)
                        return $this->display($this->_defaultmode);
                    else
                        throw new \Exception("Invalid showing mode for a field", 0, NULL);
            }
        }

    }

}
