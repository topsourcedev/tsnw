<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /*
     * da fare
     */

    class Optgroup extends Formcomponent {

        /**
         * @readwrite;
         * @var string
         */
        protected $_label;

        /**
         * @read
         * @var array
         */
        protected $_options = array();

        /**
         * @readwrite
         * @var mixed
         */
        protected $_container;
        protected $_availableattributes = array("disabled");

        public function make($html) {

            $html .= "<optgroup";
            if (isset($this->_label) && (!empty($this->_label))) {
                $html .=" label=\"{$this->_label}\"";
            }
            $html = $this->attributes2html($html);
            $html .= ">";
            foreach ($this->_options as $option) {
                $html = $option->make($html);
            }
            $html .= "</optgroup>\n";
            return $html;
        }

        /**
         * Aggiunge un'opzione al gruppo e restituisce un riferimento al gruppo per il chaining.
         * @param type $option
         * @return \WolfMVC\Template\Component\Formcomponent\Option
         */
        public function addoption($option, $flag=false){
            if ($option instanceof Option){
                $option->setContainer($this);
                $this->_options [] = $option;
                if ($flag){
                    return $this->_options[count($this->_options)-1];
                }
                return $this;
            }
        }
        public function up() {
            if (isset($this->_container) && !empty($this->_container))
                return $this->_container;
            return null;
        }

    }

}

?>