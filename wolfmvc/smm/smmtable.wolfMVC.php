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
        protected $_fields;

        /**
         * @readwrite
         * @var string 
         */
        protected $_hasKey = false;

        /**
         * @readwrite
         * @var string 
         */
        protected $_key = NULL;

        /**
         * @readwrite
         * @var string 
         */
        protected $_alias = "";

        /**
         * @readwrite
         * @var string 
         */
        protected $_name = "";
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

        /**
         * @readwrite;
         * @var array
         */
        protected $_elements = array();

        /**
         * @readwrite;
         * @var array
         */
        protected $_relations = array("from" => array(), "to" => array());

        /**
         * 
         * @readwrite
         */
        protected $_isClusterized = false;

        /**
         * 
         * @readwrite
         */
        protected $_cluster;

        /**
         * 
         * @readwrite
         */
        protected $_isEntity = false;
        
        
        /**
         *
         * @readwrite
         */
        protected $_operations = array();

        public function setMandatoryForUpdateByAlias($elements) {
            echo "<br>set mandatory in ".$this->_name."<br><br>";
            
            foreach ($elements as $key => $el) {
                echo $el->display();
                if (isset($this->_elements["byAlias"][$el->alias])){
                    $this->_elements["byAlias"][$el->alias]["mandatoryForUpdate"] = true;
                }
            }
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

        public function fromName($dbName, $tableName, $alias = "default") {
            $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
            if ($tableInfo === NULL) {
                \WolfMVC\Registry::set("systemtable_regTables", array());
            }
            if (!isset($tableInfo[$dbName]) || !isset($tableInfo[$dbName][$tableName])) {
                if (!is_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini")) {
                    throw new \Exception("No infos about this db.", 0, NULL);
                }
                $tableInfo = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini");
                $newTableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                if (!isset($tableInfo[$dbName . ".knowntables"])) {
                    throw new \Exception("No infos about this db.", 0, NULL);
                }
                $tables = $tableInfo[$dbName . ".knowntables"];
                $tables = explode("|", $tables);
                if (array_search($tableName, $tables) === FALSE) {
                    throw new \Exception("No infos about this table.", 0, NULL);
                }
                $prefix = $dbName . "." . $tableName . ".";
                if (!isset($tableInfo[$prefix . "fields"])) {
                    throw new \Exception("No field infos about this table.", 0, NULL);
                }
                $fields = $tableInfo[$prefix . "fields"];
                $fields = explode("|", $fields);
                $fieldsInfo = array();
                foreach ($fields as $key => $field) {
                    if (!isset($tableInfo[$prefix . $field . ".key"]) || !isset($tableInfo[$prefix . $field . ".extra"]) || !isset($tableInfo[$prefix . $field . ".null"]) || !isset($tableInfo[$prefix . $field . ".type"])) {
                        throw new \Exception("Missing infos about some field.", 0, NULL);
                    }
                    if ($tableInfo[$prefix . $field . ".key"] === "PRI") {
                        $this->_hasKey = true;
                        $this->_key = $field;
                    }
                    $fieldsInfo[$field] = array(
                        "extra" => $tableInfo[$prefix . $field . ".extra"],
                        "null" => $tableInfo[$prefix . $field . ".null"],
                        "type" => $tableInfo[$prefix . $field . ".type"]
                    );
                }
                $newTableInfo[$dbName] = array();
                $newTableInfo[$dbName][$tableName] = $fieldsInfo;
                \WolfMVC\Registry::set("systemtable_regTables", $newTableInfo);
                $this->_fields = $fieldsInfo;

//                if (isset($tableInfo))
            } else {
                $this->_fields = $tableInfo[$dbName][$tableName];
            }
            $this->_sourcedb = $dbName;
            $this->_sourcetable = $tableName;
            $this->_alias = $alias;
            $this->_name = $dbName . "." . $tableName;
            //aggiungo a priori l'id della tabella come campo mandatory
            if ($this->_hasKey) {
                
                $id = new Smmfieldelement(array("name" => "{{id}}", "alias" => $this->_name . ".{{id}}"));
                echo "<br>-------<br>auto id in ".$this->_name."<br>".$id->display();
                $this->addElement($id, true);
                $this->setMandatoryForUpdateByAlias(array($id));
            }
            return $this;
        }

        public function addElement($field, $flag) { // richiede che l'alias sia unico
            if (!($field instanceof Smmfield)) {
                throw new \Exception("Invalid Field", 0, NULL);
            }
            if (!($field->isWellDefined())) { //questo mi assicura che sono ben definiti alias e name
                throw new \Exception("Field is not well defined", 0, NULL);
            }
            if (isset($this->_elements["byAlias"][$field->alias])){
                throw new \Exception("Duplicate alias", 0, NULL);
            }
            $this->_elements["byName"][$field->name] = array($field);
            $this->_elements["byAlias"][$field->alias] = array($field);
            if ($flag)
                $field->addToTable($this, false);
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
                    return $this->_name . " as " . $this->_alias;
                    break;
                default:
                    if (array_search($this->_defaultmode, $this->_modes) !== FALSE)
                        return $this->display($this->_defaultmode);
                    else
                        throw new \Exception("Invalid showing mode for a field", 0, NULL);
            }
        }

        public function linkTo($tableB, $fieldA, $fieldB, $multiplicity) {
            if (!($tableB instanceof Smmtable) || !($tableB->isWellDefined())) {
                throw new \Exception("Invalid table or not well defined table", 0, NULL);
            }
            if (!(is_string($fieldA))) {
                throw new \Exception("Invalid fieldA name", 0, NULL);
            }
            if (!isset($this->_fields[$fieldA])) {
                throw new \Exception("Unknown fieldA", 0, NULL);
            }
            if (!(is_string($fieldB))) {
                throw new \Exception("Invalid fieldB name", 0, NULL);
            }
            $fieldsB = $tableB->getFields();
            if (!isset($fieldsB[$fieldB])) {
                throw new \Exception("Unknown fieldB", 0, NULL);
            }

            $rel = new Smmrelation(array(
                "tableA" => $this,
                "tableB" => $tableB,
                "fieldA" => $fieldA,
                "fieldB" => $fieldB,
                "fieldAIsPri" => ($this->_hasKey && ($this->_key === $fieldA)),
                "fieldBIsPri" => ($tableB->_hasKey && ($tableB->_key === $fieldB)),
                "multiplicity" => $multiplicity
            ));
            $this->addtoRelations("from", $rel);
            $tableB->addtoRelations("to", $rel);
        }

        protected function addtoRelations($sense, $rel) {
            switch ($sense) {
                case 'from':
                    array_push($this->_relations["from"], $rel);
                    break;
                case 'to':
                    array_push($this->_relations["to"], $rel);
                    break;
                default :
                    throw new \Exception("Invalid sense for relation", 0, NULL);
            }
        }

        public function debug_describe($what, $level) {
            echo "<BR>TABELLA<BR><BR>";
            echo "fields :<br>";
            foreach ($this->_fields as $key => $field){
                echo $key.", ";
            }
            echo "<br><br>elements:<br>";
            foreach ($this->_elements["byAlias"] as $key => $el){
                echo "<br>name: ".$el[0]->name.", alias: ".$el[0]->alias.", mandatory: ".((isset($el["mandatoryForUpdate"]) && $el["mandatoryForUpdate"]) === TRUE ? "si" : "no")."<br>";
            }
            parent::debug_describe($what, $level);
        }

    }

}
