<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Smm {

    /**
     * Semantic Multi Model
     */
    class Smmfieldmix extends Smmfield {

        /**
         * @readwrite
         * @var array
         */
        protected $_components;
        

        /**
         * @readwrite
         * @var string 
         */
        protected $_alias;
        
         /**
         * @readwrite
         * @var string 
         */
        protected $_name;
        protected $_filters = array();

        /**
         * @readwrite
         * @string
         */
        protected $_defaultmode = "name";
        
        /**
         * @readwrite
         * @var string 
         */
        protected $_tablealias;

        /**
         * @readwrite
         * @var string
         */
        protected $_fieldOpName;
        
        /**
         * @readwrite
         * @var string
         */
        protected $_fieldOp;
        
        public function __construct($options = array()) {
            if (!isset($options["fieldOpName"]) || !is_string($options["fieldOpName"])){
                throw new \Exception("Can't create a fieldmix without an operation", 0, NULL);
            }
            if (!isset($options["alias"]) || !is_string($options["alias"])){
                throw new \Exception("Can't create a fieldmix without an alias", 0, NULL);
            }
            if (!isset($options["components"]) || !is_array($options["components"])){
                throw new \Exception("Can't create a fieldmix without components", 0, NULL);
            }
            foreach ($options["components"] as $key => $comp){
                if (!($comp instanceof Smmfield)){
                    throw new \Exception("Can't create a fieldmix: component #".$key." is not a valid field", 0, NULL);
                }
            } 
            parent::__construct($options);
            
            $this->_fieldOp = new Smmfieldoperation();
            $this->_fieldOp->fromName($this->_fieldOpName);
            foreach ($this->_components as $key => $comp){
                $this->_fieldOp->setArgument($key, $comp);
            }
            $this->_name = $this->_fieldOp->expose();
            $this->_collapse = $this->_fieldOp->getCollapse();
            $this->_defaultmode = "name";
            array_push($this->_modes,"name");
        }

    }

}
