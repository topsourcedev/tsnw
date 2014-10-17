<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmcluster extends Smmtable {

        /**
         * @readwrite
         * @var string 
         */
        protected $_tables = array();

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
         *
         * @readwrite
         */
        protected $_keyFromTable;

        /**
         * @readwrite;
         * @var array
         */
        protected $_elements = array();

        public function isWellDefined() {
            if (!(is_string($this->_name)) || strlen($this->_name) < 1) {
                return false;
            }
            if (!(is_string($this->_alias)) || strlen($this->_alias) < 1) {
                return false;
            }
            return true;
        }

        public function addElement($field, $flag) {
            if (!($field instanceof Smmfield)) {
                throw new \Exception("Invalid Field", 0, NULL);
            }
            if (!($field->isWellDefined())) {
                throw new \Exception("Field is not well defined", 0, NULL);
            }
            array_push($this->_elements, $field);
            if ($flag)
                $field->addToTable($this, false);
            return $this;
        }


        /**
         * Richiede obbligatoriamente: array tables - le tabelle da inserire, idFromTable - da quale tabella prendere l'id (indice nel vettore precedente) 
         * @param array $options
         * @throws \Exception
         */
        public function __construct($options = array()) {


            if (!isset($options["name"]))
                $this->_name = "nome da definire";
            if (!isset($options["alias"]))
                $this->_alias = "alias da definire";
            if (!isset($options["tables"]) || !(is_array($options["tables"])))
                throw new \Exception("Invalid tables argument in cluster creation", 0, NULL);
            if (!isset($options["idFromTable"])) {
                throw new \Exception("Missing id choice in cluster creation", 0, NULL);
            }
            foreach ($options["tables"] as $key => $tab) {
                if (!($tab instanceof Smmtable)) {
                    throw new \Exception("Invalid argument table in cluster creation", 0, NULL);
                }
                array_push($this->_tables, $tab);
                if ($key === $options["idFromTable"]) {
                    if ($tab->_hasKey) {
                        $this->_hasKey = true;
                        if ($tab instanceof Smmcluster) {
                            $this->_key = array($tab->_key);
                        }
                        else {
                            $this->_key = array($tab, $tab->_key);
                        }
                    }
                }
            }
            /**
             * @todo migliorare controllo su isEntity
             */
            unset($options["tables"]);
            unset($options["idFromTable"]);
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

    }

}
