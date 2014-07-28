<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmtable extends \WolfMVC\Base {

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
         * @readwrite;
         * @var string
         */
        protected $_sourcedb;
        /**
         * @readwrite;
         * @var string
         */
        protected $_sourcetable;

        
        
        public function fromName($dbName,$tableName){
            $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
            if ($tableInfo === NULL){
                \WolfMVC\Registry::set("systemtable_regTables",array());
            }
            if (!isset($tableInfo[$dbName][$tableName])){
                if (!is_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini")){
                    throw new \Exception("No infos about this db.", 0, NULL);
                }
                $tableInfo = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini");
                if ($isset[$dbName][$tableName])
                \WolfMVC\Registry::set("systemtable_regTables", $tableInfo);
                echo "<pre>";
                print_r($tableInfo);
                echo "</pre>";
//                if (isset($tableInfo))
            }
            else {
                $tableInfo = $tableInfo[$dbName][$tableName];
            }
            
            return $this;
            
        }





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
