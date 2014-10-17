<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmstructure extends \WolfMVC\Base {

        /**
         * @readwrite
         * @var array 
         */
        protected $_fields = array();

        /**
         * @readwrite
         * @var array 
         */
        protected $_calcFields = array();

        /**
         * @readwrite
         * @var array 
         */
        protected $_tables = array();

        /**
         * @readwrite
         */
        protected $_maintable;

        /**
         * @readwrite
         * @var array
         */
        protected $_entities = array();

        /**
         *
         * @readwrite
         */
        protected $_relations = array("byStart" => array(), "byEnd" => array());

        /**
         * @readwrite
         * @var string
         */
        protected $_defaultDb = "";

        public function addTable($tableName, $alias, $ignoreCheck = false, $bypass_hasKey = false, $bypass_key = "") {
            if (!isset($this->_defaultDb) || ($this->_defaultDb === ""))
            {
                throw new \Exception("Db not selected", 0, null);
            }
            $dbName = $this->_defaultDb;
            if (isset($this->_tables[$alias]))
            {
                throw new \Exception("Duplicate alias in tables", 0, null);
            }
            if (!$ignoreCheck)
            {
                $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                if ($tableInfo === NULL)
                {
                    \WolfMVC\Registry::set("systemtable_regTables", array());
                }
                if (!isset($tableInfo[$dbName]) || !isset($tableInfo[$dbName][$tableName]))
                {
                    if (!is_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini"))
                    {
                        throw new \Exception("No infos about this db.", 0, NULL);
                    }
                    $tableInfo = parse_ini_file(APP_PATH . "/application/configuration/database/" . $dbName . ".ini");
                    $newTableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                    if (!isset($tableInfo[$dbName . ".knowntables"]))
                    {
                        throw new \Exception("No infos about this db.", 0, NULL);
                    }
                    $tables = $tableInfo[$dbName . ".knowntables"];
                    $tables = explode("|", $tables);
                    if (array_search($tableName, $tables) === FALSE)
                    {
                        throw new \Exception("No infos about this table.", 0, NULL);
                    }
                    $prefix = $dbName . "." . $tableName . ".";
                    if (!isset($tableInfo[$prefix . "fields"]))
                    {
                        throw new \Exception("No field infos about this table.", 0, NULL);
                    }
                    $fields = $tableInfo[$prefix . "fields"];
                    $fields = explode("|", $fields);
                    $fieldsInfo = array();
                    $hasKey = false;
                    $tablekey = "";
                    foreach ($fields as $key => $field) {
                        if (!isset($tableInfo[$prefix . $field . ".key"]) || !isset($tableInfo[$prefix . $field . ".extra"]) || !isset($tableInfo[$prefix . $field . ".null"]) || !isset($tableInfo[$prefix . $field . ".type"]))
                        {
                            throw new \Exception("Missing infos about some field.", 0, NULL);
                        }
                        if ($tableInfo[$prefix . $field . ".key"] === "PRI")
                        {
                            $hasKey = true;
                            $tablekey = $field;
                        }
                        $fieldsInfo[$field] = array(
                            "extra" => $tableInfo[$prefix . $field . ".extra"],
                            "null" => $tableInfo[$prefix . $field . ".null"],
                            "type" => $tableInfo[$prefix . $field . ".type"]
                        );
                    }
                    if (!isset($newTableInfo[$dbName]))
                        $newTableInfo[$dbName] = array();
                    $newTableInfo[$dbName][$tableName] = array("fields" => $fieldsInfo, "hasKey" => $hasKey, "key" => $tablekey);
                    \WolfMVC\Registry::set("systemtable_regTables", $newTableInfo);
                }
                $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                $tableInfo = $tableInfo[$dbName][$tableName];
            }
            $table = new \stdClass();
            $table->dbName = $this->_defaultDb;
            $table->tableName = $tableName;
            $table->alias = $alias;
            $table->belongsToEntity = false;
            if (!$ignoreCheck)
            {
                $table->hasKey = $tableInfo["hasKey"];
                $table->key = $tableInfo["key"];
                if (count($this->_tables) === 0)
                    $this->_maintable = $alias;
            }
            else
            {
                $table->hasKey = $bypass_hasKey;
                $table->key = $bypass_key;
            }
            $this->_tables[$alias] = $table;
            return $this;
        }

        public function removeTable($tablealias) {

            unset($this->_tables[$tablealias]);
            return $this;
        }

        public function setEntity($entityName, $tables) {
            if (!is_array($tables))
            {
                throw new \Exception("Tables must be an array.", 0, null);
            }
            $entity = new \stdClass();
            $entity->numberOfTables = count($tables);
            $entity->entityName = $entityName;
            $index = 0;
            foreach ($tables as $k => $alias) {
                if (!isset($this->_tables[$alias]))
                {
                    throw new \Exception("Unknown table " . $alias . ".", 0, null);
                }
                $entity->{"table_" . $index++} = $alias;
                $this->_tables[$alias]->belongsToEntity = $entityName;
            }
            $this->_entities[$entityName] = $entity;
        }

        public function removeEntity($entityName) {
            if (isset($this->_entities[$entityName]))
                unset($this->_entities[$entityName]);
        }

        public function setRelation($tableA, $tableB, $mult, $fieldA, $fieldB, $ignoreCheck = false) {
            $relation = new \stdClass();
            if (!isset($this->_tables[$tableA]))
            {
                throw new \Exception("TableA not found in the structure.", 0, null);
            }
            if (!isset($this->_tables[$tableB]))
            {
                throw new \Exception("TableB not found in the structure.", 0, null);
            }
            if (!(in_array($mult, array("!", "+", "*"))))
            {
                throw new \Exception("Invalid relation multiplicity.", 0, null);
            }
            if (!$ignoreCheck)
            {
                $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                $tableNameA = $this->_tables[$tableA]->tableName;
                $dbNameA = $this->_tables[$tableA]->dbName;

                $tableInfoA = $tableInfo[$dbNameA][$tableNameA];
                $fieldInfoA = $tableInfoA["fields"];
                if (!isset($fieldInfoA[$fieldA]))
                {
                    throw new \Exception("FieldA not found in TableA.", 0, null);
                }
                $fromKey = false;
                if ($tableInfoA["hasKey"] && $tableInfoA["key"] === $fieldA)
                {
                    $fromKey = true;
                }

                $tableNameB = $this->_tables[$tableB]->tableName;
                $dbNameB = $this->_tables[$tableB]->dbName;

                $tableInfoB = $tableInfo[$dbNameB][$tableNameB];
                $fieldInfoB = $tableInfoB["fields"];
                if (!isset($fieldInfoB[$fieldB]))
                {
                    throw new \Exception("FieldB not found in TableB.", 0, null);
                }
                $toKey = false;
                if ($tableInfoB["hasKey"] && $tableInfoB["key"] === $fieldB)
                {
                    $toKey = true;
                }
            }
            $relation->from = $tableA;
            $relation->to = $tableB;
            $relation->mult = $mult;
            $relation->fieldFrom = $fieldA;
            $relation->fieldTo = $fieldB;
            $relation->fromKey = (isset($fromKey) ? $fromKey : false);
            $relation->toKey = (isset($toKey) ? $toKey : false);


            if (!isset($this->_relations["byStart"][$tableA]))
            {
                $this->_relations["byStart"][$tableA] = array();
            }
            if (!isset($this->_relations["byEnd"][$tableB]))
            {
                $this->_relations["byEnd"][$tableB] = array();
            }
            array_push($this->_relations["byStart"][$tableA], $relation);
            array_push($this->_relations["byEnd"][$tableB], $relation);
            return $this;
        }

        /**
         * Imposta una picklist come origine dati per gli update: bisogna specificare: per quale campo si definisce la picklist,
         * quali sono i valori della picklist
         * @param string $fieldalias
         * @throws \Exception
         */
        public function setStaticPicklist($fieldAlias,$values){
            if (!is_array($values)){
                throw new \Exception("Values must be an array!",0,null);
            }
            if (isset($this->_fields[$fieldAlias])){
                $this->_fields[$fieldAlias]->picklist = new \stdClass();
                $this->_fields[$fieldAlias]->picklist->type = "static";
                $this->_fields[$fieldAlias]->picklist->values = $values;
            }
            else {
                throw new \Exception("Unknown field ".$fieldAlias,0,null);
            }
        }
        
        /**
         * Imposta una picklist come origine dati per gli update: bisogna specificare: per quale campo si definisce la picklist,
         * db e tabella di origine della picklist, il campo dove sono contenuti i dati e quello (eventuale) che contiene la chiave del riferimento
         * @param string $fieldalias
         * @throws \Exception
         */
        public function setDbDrivenPicklist($fieldAlias, $structure, $dataField, $linkField){
            if (isset($this->_fields[$fieldAlias])){
                $this->_fields[$fieldAlias]->picklist = new \stdClass();
                $this->_fields[$fieldAlias]->picklist->type = "dbdriven";
                $this->_fields[$fieldAlias]->picklist->structure = $structure;
                $this->_fields[$fieldAlias]->picklist->dataFieldName = $dataField;
                $this->_fields[$fieldAlias]->picklist->linkFieldName = $linkField;
            }
            else {
                throw new \Exception("Unknown field ".$fieldAlias,0,null);
            }
        }
        
        public function deleteRelations($table) {
            unset($this->_relations["byStart"][$table]);
            unset($this->_relations["byEnd"][$table]);
            $relations = $this->_relations["byStart"];
            foreach ($relations as $tablefrom => $rel) {
                foreach ($rel as $k => $r) {

                    if ($r->from === $table)
                    {
                        unset($this->_relations["byStart"][$tablefrom][$k]);
                    }
                    if ($r->to === $table)
                    {
                        unset($this->_relations["byStart"][$tablefrom][$k]);
                    }
                }
            }
            $relations = $this->_relations["byEnd"];
            foreach ($relations as $tableto => $rel) {
                foreach ($rel as $k => $r) {
                    if ($r->from === $table)
                    {
                        unset($this->_relations["byEnd"][$tableto][$k]);
                    }
                    if ($r->to === $table)
                    {
                        unset($this->_relations["byEnd"][$tableto][$k]);
                    }
                }
            }
        }

        public function addField($fieldName, $table, $alias, $ignore_table = false, $tableAlias = "") {
            $field = new \stdClass();
            if (isset($this->_fields[$alias]) || isset($this->_calcFields[$alias]))
            {
                throw new \Exception("Duplicate field alias.", 0, null);
            }
            if (!$ignore_table && !isset($this->_tables[$table]))
            {
//                echo "asdasd".($table);
//                exit;
                throw new \Exception("Table not found in the structure.", 0, null);
            }
            if (!$ignore_table)
            {
                $table = $this->_tables[$table];
                $dbName = $table->dbName;
                $tableName = $table->tableName;
                $tableInfo = \WolfMVC\Registry::get("systemtable_regTables");
                $tableInfo = $tableInfo[$dbName][$tableName];
                $fieldInfo = $tableInfo["fields"];
                if (!isset($fieldInfo[$fieldName]))
                {
                    throw new \Exception("Unknown field " . $fieldName . " in table " . $tableName . ".", 0, null);
                }
                $field->table = $table->alias;
                if ($alias == $table->alias . "_PK")
                {
                    throw new \Exception("Invalid alias for field: " . $table->alias . "_PK", 0, null);
                }

                $field->type = $fieldInfo[$fieldName]["type"];
            }
            else
            {
                $field->table = $tableAlias;
                $field->type = "";
            }
            $field->isCalc = false;
            $field->fieldName = $fieldName;
            $field->select = true;
            $field->editable = false;
            $this->_fields[$alias] = $field;
            return $this;
        }

        public function setGroupingField($fieldalias, $groupfieldalias) { //se usato in una tabella raggruppata estrae il campo insieme ad un'etichetta
            if (isset($this->_fields[$fieldalias]))
            {
                $this->_fields[$fieldalias]->grouping = $groupfieldalias;
            }
            else if (isset($this->_calcFields[$fieldalias]))
            {
                $this->_calcFields[$fieldalias]->grouping = $groupfieldalias;
            }
            else
            {
                throw new \Exception("Unknown Field " . $fieldalias, 0, null);
            }
            return $this;
        }

        public function setGroupingTable($tablealias, $groupfieldalias) { // raggruppa una tabella per un campo
            if (isset($this->_tables[$tablealias]))
            {
                $this->_tables[$tablealias]->grouping = $groupfieldalias;
            }
            else
            {
                throw new \Exception("Unknown Table " . $tablealias, 0, null);
            }
            return $this;
        }

        public function fieldExists($fieldAlias) {
            return (isset($this->_fields[$fieldAlias]));
        }

        public function changeFieldForSubquery($fieldalias, $newtable, $newFieldName) {
            if (!isset($this->_fields[$fieldalias]))
            {
                throw new \Exception("Invalid field " . $fieldalias, 0, null);
            }
            $this->_fields[$fieldalias]->table = $newtable;
            $this->_fields[$fieldalias]->fieldName = $newFieldName;
            return $this;
        }

        public function changeCalcFieldToFieldForSubquery($fieldalias, $newtable, $newFieldName) {
            if (!isset($this->_calcFields[$fieldalias]))
            {
                throw new \Exception("Invalid field " . $fieldalias, 0, null);
            }
            $field = $this->_calcFields[$fieldalias];
            unset($this->_calcFields[$fieldalias]);
            $field->table = $newtable;
            unset($field->tables);
            $field->isCalc = false;
            $field->fieldName = $newFieldName;
            unset($field->expression);
            unset($field->fields);
            $this->_fields[$fieldalias] = $field;
            return $this;
        }

        //da migliorare con supporto tabella in db
        public function addCalculatedField($regOpExpression, $fields, $alias, $overwriteComps = false, $ignoreCheck = false, $ext = array()) {
            if (isset($this->_fields[$alias]) || isset($this->_calcFields[$alias]))
            {
                throw new \Exception("Duplicate field alias.", 0, null);
            }
            $tables = array();
            foreach ($fields as $k => $field) {
                if (isset($this->_fields[$field]))
                {
                    if (!in_array($this->_fields[$field]->table, $tables))
                        array_push($tables, $this->_fields[$field]->table);
                    $fields[$k] = array($field, "smooth");
                    if ($overwriteComps)
                        $this->_fields[$field]->select = false;
                }
                else if (isset($this->_calcFields[$field]))
                {
                    if (!in_array($this->_calcFields[$field]->tables, $tables))
                        array_merge($tables, $this->_calcFields[$field]->tables);
                    $fields[$k] = array($field, "calc");
                    if ($overwriteComps)
                        $this->_calcFields[$field]->select = false;
                }
                else if ($ignoreCheck)
                {
                    $fields = $ext;
                }
                else
                {

                    throw new \Exception("Unknown field " . $field . " in structure.", 0, null);
                }
            }

            $field = new \stdClass();
            $field->tables = $tables;
            $field->type = "";
            $field->isCalc = true;
            $field->expression = $regOpExpression;
            $field->fields = $fields;
            $field->select = true;
            $field->editable = false;
            $field->overwriteComps = $overwriteComps;
            $this->_calcFields[$alias] = $field;

            return $this;
        }

        public function addFilterToTable($table, $cond) { // il campo è indicato in cond con ##FIELD##
            if (isset($this->_tables[$table]))
            {
                if (!isset($this->_tables[$table]->filters) || !is_array($this->_tables[$table]->filters))
                    $this->_tables[$table]->filters = array();
                array_push($this->_tables[$table]->filters, $cond);
            }
            else
            {
                throw new \Exception("Unknown table ".$table, 0, null);
            }
            return $this;
        }

        public function addFilterToField($field, $cond) { // il campo è indicato in cond con ##FIELD##
            $alias = $field;
            if (isset($this->_fields[$alias]))
            {
                if (!isset($this->_fields[$alias]->filters) || !is_array($this->_fields[$alias]->filters))
                    $this->_fields[$alias]->filters = array();
                array_push($this->_fields[$alias]->filters, $cond);
            } else if (isset($this->_calcFields[$alias]))
            {
                if (!isset($this->_calcFields[$alias]->filters) || !is_array($this->_calcFields[$alias]->filters))
                    $this->_calcFields[$alias]->filters = array();
                array_push($this->_calcFields[$alias]->filters, $cond);
            } else
            {
                throw new \Exception("Unknown field.", 0, null);
            }
            return $this;
        }

        public function setFieldsEditable($fields) {
            if (is_array($fields))
            { // caso multiplo
                foreach ($fields as $fieldk => $field) {
                    if (isset($this->_fields[$field]))
                    {
                        $this->_fields[$field]->editable = true;
                    }
                    else if (isset($this->_calcFields[$field]))
                    {
                        $this->_calcFields[$field]->editable = true;
                    }
                    else
                    {
                        throw new \Exception("Unknown field.", 0, NULL);
                    }
                }
            }
            else if (is_string($fields))
            { //caso singolo
                $field = $fields;
                if (isset($this->_fields[$field]))
                {
                    $this->_fields[$field]->editable = true;
                }
                else if (isset($this->_calcFields[$field]))
                {
                    $this->_calcFields[$field]->editable = true;
                }
                else
                {
                    throw new \Exception("Unknown field.", 0, NULL);
                }
            }
        }

        public function setFieldSelectable($field, $value) {
            if (isset($this->_fields[$field]))
            {
                $this->_fields[$field]->select = $value;
            }
            else if (isset($this->_calcFields[$field]))
            {
                $this->_calcFields[$field]->select = $value;
            }
            else
            {
                throw new \Exception("Unknown field.", 0, NULL);
            }
        }

        public function defineUpdateField($fieldalias){
            
        }
        
        
        
    }

}
