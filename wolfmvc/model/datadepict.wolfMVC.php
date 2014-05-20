<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Model {

    /**
     * Classe base dei visualizzatori di dati
     */
    class Datadepict extends \WolfMVC\Multimodel{

        /**
         * @readwrite
         * @var array
         */
        protected $_tables = array();

        /**
         * @readwrite
         * @var array
         */
        protected $_tablesalias = array();

        
        /**
         * @readwrite
         * @var array
         */
        protected $_fields = array();

        /**
         * @readwrite
         * @var array
         */
        protected $_fieldsalias = array();
        
        /**
         * @readwrite
         * @var string
         */
        protected $_sourcetype;

        /**
         * @readwrite
         * @var string
         */
        protected $_source;
        
        /**
         *
         * @var string
         */
        protected $_params;
        
        
        /**
         * @readwrite
         * @var array
         */
        protected $_where = array();
        
        public function createSql() {
         
            $SQL = "SELECT ";
            $SQL .=join(", ", $this->_fields);
            $SQL .= " FROM ";
            $SQL .=join(" JOIN ", $this->_tables);
            if (isset($this->_where) && !(empty($this->_where))){
                $SQL .=" WHERE " . join(" AND ",  $this->_where);
            }
//            echo $SQL;
            return $SQL;
        }

        

        public function addField($type, $field, $alias, $table, $table_alias, $id1 = "", $table2 = "", $id2 = "", $args = "") {
            if (!isset($this->_tables[$table])) { // caso prima occorrenza della tabella
                switch ($type) {
                    case 0: //normal
                    case '0':
                    case 1: // fixed picklist
                    case '1':

                        $this->_tables[$table] = $table . " " . $table_alias;
                        $this->_tablesalias[$table] = $table_alias;
                        break;
                    case 2: //related record field
                    case '2':
                    case 3: //related record field + fixed picklist
                    case '3':
                    case 4: //picklist
                    case '4':

                        $this->_tables[$table] = $table . " " . $table_alias . " ON " . $table_alias . "." . $id1 . " = " . $this->_tablesalias[$table2] . "." . $id2;
                        $this->_tablesalias[$table] = $table_alias;
                }
            }
            if (($type == 10) || ($type == '10')) {
                $arg = array();
                for ($i = 0; $i < count($args);) {
                    $arg [] = $this->_tablesalias[$args[$i + 1]] . "." . $args[$i];
                    $i+=2;
                }
                $field = vsprintf($field, $arg);
                $this->_fields[$field] = $field . " AS '" . $alias . "'";
                $this->_fieldsalias [$field] = $alias;
            }
            else {
                $this->_fields[$field] = $table_alias . "." . $field . " AS '" . $alias . "'";
                $this->_fieldsalias [$field] = $alias;
            }
            return $this;
        }

        public function describe() {
            echo "<br>";
            echo "sono un datadepict<br>";
            echo "tabelle:<pre>";
            print_r($this->_tables);
            echo "</pre>aliases:<pre>";
            print_r($this->_tablesalias);
            echo "</pre>campi:<pre>";
            print_r($this->_fields);
            echo "</pre>dati:<pre>";
            print_r($this->_data);
            echo "</pre>";
        }
        
        public function Data() {
            return $this->_data;
        }
        
//        public function getAllFromDb() {
//            $this->_data = array("Dati");
//        }
        }

}