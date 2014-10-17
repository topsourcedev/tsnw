<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmrelation extends \WolfMVC\Base {

        /**
         * @readwrite
         */
        protected $_tableA;

        /**
         * @readwrite
         */
        protected $_tableB;
        
        /**
         * @readwrite
         */
        protected $_fieldA;
        
        /**
         * @readwrite
         */
        protected $_fieldB;
        
         /**
         * @readwrite
         */
        protected $_fieldAIsPri = false;
        
        /**
         * @readwrite
         */
        protected $_fieldBIsPri = false;
        
        /**
         * @readwrite
         * @var string 
         */
        protected $_multiplicity = "";
        
        
        public function __construct($options = array()) {
            
            if (!isset($options["tableA"]) || !($options["tableA"] instanceof Smmtable) || !($options["tableA"]->isWellDefined())){
                throw new \Exception("Invalid table A or not well defined table", 0, NULL);
            }
            if (!isset($options["tableB"]) || !($options["tableB"] instanceof Smmtable) || !($options["tableB"]->isWellDefined())){
                throw new \Exception("Invalid table B or not well defined table", 0, NULL);
            }
            if (!isset($options["fieldA"]) || !(is_string($options["fieldA"]))){
                throw new \Exception("Invalid fieldA or not well defined field", 0, NULL);
            }
            if (!isset($options["fieldB"]) || !(is_string($options["fieldB"]))){
                throw new \Exception("Invalid fieldB or not well defined field", 0, NULL);
            }
            if (!isset($options["multiplicity"]) || !(is_string($options["multiplicity"]))){
                throw new \Exception("Invalid multiplicity", 0, NULL);
            }
            
            parent::__construct($options);
            
        }
        
        public function isWellDefined(){
            if (!($this->_tableA instanceof Smmtable)){
                return false;
            }
            if (!($this->_tableB instanceof Smmtable)){
                return false;
            }
            if (!($this->_fieldA instanceof Smmfield)){
                return false;
            }
            if (!($this->_fieldB instanceof Smmfield)){
                return false;
            }
            return true;
        }
        
        
    }

}
