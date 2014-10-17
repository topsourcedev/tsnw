<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template {


    abstract class Pagecomponent extends \WolfMVC\Base {

        /**
         *
         * @readwrite
         */
        protected $_sizeInfo;

        /**
         *
         * @readwrite
         */
        protected $_data;

        /**
         *
         * @readwrite
         */
        protected $_labels;

        /**
         * @readwrite
         * @var array
         */
        protected $_titleInfo;

        /**
         *
         * @var readwrite
         */
        protected $_parameters = array();

        /**
         *
         * @readwrite
         */
        protected $_id;

        /**
         *
         * @readwrite
         */
        protected $_regOperations = array();

        /**
         * I comp element sono classi del DOM
         * @readwrite
         */
        protected $_compElements = array();
        
        /**
         * @readwrite
         * @var array
         */
        protected $_initOperations = array();

        public function render($html) {
            return "<div class=\"pageComponent\">" . $html . "<div>";
        }

        public function setParameter($key, $value) {
            $this->_parameters[$key] = $value;
        }

        public function wrapOperations() {
            //ad un certo elemento è stata associata un'operazione
            //traduco l'operazione in codice javascript
            $script = "function setOps(){";
            foreach ($this->_compElements as $compk => $comp) {
                
            }
        }

        public function setPageOperation($compElement, $event, $regOp) { //"redirect|".SITE_PATH . $this->nameofthiscontroller() . "/assegnazione/{{param1}}|Pk_project"
            if (!isset($this->_regOperations) || !is_array($this->_regOperations))
            {
                $this->_regOperations = array();
            }
            if (!isset($this->_compElements[$compElement]))
            {
                throw new \Exception("Unknown comp Element", 0, null);
            }
            $elem = $this->_compElements[$compElement];
            if (!isset($elem["events"][$event]))
            {
                throw new \Exception("Unknown event for comp Element ".$compElement, 0, null);
            }
            $ev = $elem["events"][$event];
//            if (!isset())
            
        }

        abstract public function renderTitle();

        abstract public function includingScript();

        abstract public function includingCss();
    }

}
?>