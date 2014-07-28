<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Operazione fra campi
     */
    class Smmfieldoperation extends \WolfMVC\Base {

        /**
         * @readwrite
         * @var string Numero di argomenti come stringa. Sono ammessi valori 0,1,2...,* = 0 o più, + = 1 o più
         */
        protected $_numberOfArguments;

        /**
         * @readwrite
         * @var string Espressione del'operatore 
         */
        protected $_expression;

        /**
         * @readwrite
         * @var string Espressione del'operatore 
         */
        protected $_argumentTypes = array();

        /**
         * @readwrite
         * @var string Espressione del'operatore 
         */
        protected $_returnType = array();

        /**
         * @readwrite
         * @var string Espressione del'operatore 
         */
        protected $_canBeNull = array();

        /**
         * @readwrite
         * @var array
         */
        protected $_arguments = array();

        public function fromName($name) {
            $regOperations = (\WolfMVC\Registry::get("smm_regOperations"));
            if ($regOperations === NULL) {
                $st = \WolfMVC\Registry::get("systemtable_smmfieldoperation");
                /**
                 * @todo controllo presenza dati su tabella   ^^^^^^^^^
                 */
                $database = \WolfMVC\Registry::get("database_" . $st["db"]);
                $database->connect();

                $all = $database->query();
                $all->setRawsql("SELECT * FROM " . $st["table"]);
                $all = $all->all();
                foreach ($all as $key => $row) {
                    $regOperations[$row["name"]] = array(
                        "numberOfArguments" => $row["numberofarguments"],
                        "expression" => $row["expression"],
                        "argumentTypes" => $row["argumenttypes"],
                        "returnType" => $row["returntype"],
                        "canBeNull" => $row["canbenull"]
                    );
                }
                \WolfMVC\Registry::set("smm_regOperations", $regOperations);
            }
            if (isset($regOperations[$name])) {
                $this->_canBeNull = $regOperations[$name]["canBeNull"];
                $this->_expression = $regOperations[$name]["expression"];
                $this->_argumentTypes = $regOperations[$name]["argumentTypes"];
                $this->_returnType = $regOperations[$name]["returnType"];
                $this->_numberOfArguments = $regOperations[$name]["numberOfArguments"];
                return $this;
            } else {
                throw new \Exception("Operazione inesistente.", 0, NULL);
            }
        }

        public function isWellDefined() {
            if ((!isset($this->_numberOfArguments))) {
                throw new \Exception("Operation not well defined: missing number of arguments", 0, NULL);
            }
            if ((!isset($this->_argumentTypes))) {
                throw new \Exception("Operation not well defined: missing arguments types", 0, NULL);
            }
            if ((!isset($this->_canBeNull))) {
                throw new \Exception("Operation not well defined: missing can be null statement", 0, NULL);
            }
            if ((!isset($this->_expression))) {
                throw new \Exception("Operation not well defined: missing op expression", 0, NULL);
            }
            if ((!isset($this->_returnType))) {
                throw new \Exception("Operation not well defined: missing return type", 0, NULL);
            }
        }

        public function setArgument($index, $field) {
            $this->isWellDefined();
            if (!is_int($index) || !($field instanceof Smmfield)) {
                throw new \Exception("Invalid argument for an operation", 0, NULL);
            }
//            if ($index < 0 || $index > $this->_numberOfArguments) {
//                throw new \Exception("Too many arguments for an operation", 0, NULL);
//            }
            $this->_arguments[$index] = $field;
            return $this;
        }

        
        public function expose() {
            $this->isWellDefined();
            $exp = $this->_expression;
            $num = $this->_numberOfArguments;

            if (((int)$num)."" === $num) {
                echo "#argomenti = " . $num . "<br>";
                if (count($this->_arguments) !== (int)$num) {
                    throw new \Exception("Wrong number of arguments for an operation", 0, NULL);
                }
                for ($i = 0; $i < $num; $i++) {
                    $arg = $this->_arguments[$i];
                    $exp = str_replace("##" . ($i + 1) . "##", $arg->display(), $exp);
                }
            }
            switch ($num) {
                case '*':
                    $argnum = count($this->_arguments);
                    $args = array();
                    foreach ($this->_arguments as $key => $arg) {
                        array_push($args, $arg->display());
                    }
                    $args = join(",", $args);
                    $exp = str_replace("##*##", $args, $exp);
                    break;
                case '+':
                    if (count($this->_arguments) === 0) {
                        throw new \Exception("Wrong number of arguments for an operation", 0, NULL);
                    }
                    $args = array();
                    foreach ($this->_arguments as $key => $arg) {
                        array_push($args, $arg->display());
                    }
                    $args = join(",", $args);
                    $exp = str_replace("##+##", $args, $exp);
                    break;
                case '++':
                    if (count($this->_arguments) <= 1) {
                        throw new \Exception("Wrong number of arguments for an operation", 0, NULL);
                    }
                    $args = array();
                    foreach ($this->_arguments as $key => $arg) {
                        array_push($args, $arg->display());
                    }
                    $exp = str_replace("##1##", array_shift($args), $exp);

                    $args = join(",", $args);
                    $exp = str_replace("##+##", $args, $exp);
                    break;
            }
            return $exp;
        }

    }

}
    