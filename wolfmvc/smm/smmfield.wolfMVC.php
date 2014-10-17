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

        /**
         * 
         * @readwrite
         */
        protected $_table;
        
        /**
         * @readwrite
         * @var boolean
         */
        protected $_collapse = false;

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

        public function addToTable($table, $flag) {
            if (!($table instanceof Smmtable)) {
                throw new \Exception("Invalid Table", 0, NULL);
            }
            if (!($table->isWellDefined())) {
                throw new \Exception("Table is not well defined", 0, NULL);
            }
            $this->_table = $table;
            if ($flag)
                $this->_table->addElement($this,false);
            return $this;
        }

        public function addToCluster($cluster, $flag) {
            if (!($cluster instanceof Smmcluster)) {
                throw new \Exception("Invalid Cluster", 0, NULL);
            }
            if (!($cluster->isWellDefined())) {
                throw new \Exception("Cluster is not well defined", 0, NULL);
            }
            $this->_table = $cluster;
            if ($flag)
                $this->_table->addElement($this,false);
            return $this;
        }

        
        public function isWellDefined() {
            if (!(is_string($this->_name)) || strlen($this->_name) < 1) {
                return false;
            }
            if (!(is_string($this->_alias)) || strlen($this->_alias) < 1) {
                return false;
            }
            return true;
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
                    return $this->_name . " as " . $this->_alias;
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
