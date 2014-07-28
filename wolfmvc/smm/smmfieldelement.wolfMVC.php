<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmfieldelement extends Smmfield {

        
        
        /**
         * @readwrite
         * @string
         */
        protected $_defaultmode = "name";
        
        /**
         * @readwrite
         * @var string 
         */
        protected $_modes = array("name","alias");
        
        public function __construct($options = array()) {
            if (!isset($options["name"]) || !isset($options["alias"])){
                throw new \Exception("Can't create a fieldelement without a name and an alias", 0, NULL);
            }
            parent::__construct($options);
            
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
