<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    class Select extends Formcomponent {

        /**
         * @readwrite
         * @var string
         */
        protected $_label;

        /**
         * @read
         * @var string
         */
        protected $_type = "text";
        
        /**
         * @read
         * @var array Può contenere oggetti di classe Option o Optgroup 
         */
        protected $_options = array();
        
        protected $_availableattributes = array("autofocus", "disabled", "name", "required", "size");

        public function make($html) {
            if (isset($this->_label) && (!empty($this->_label)) && (!empty($this->_name))) {
                $html .="<label for=\"{$this->_name}\">{$this->_label}</label>\n";
            }
            $html .= "<select";
            $html = $this->attributes2html($html);
            $html .= ">\n";
            foreach ($this->_options as $option){
                $html = $option->make($html);
            }
            $html .= "</select>\n";
            return $html;
        }
        
        /**
         * Aggiunge una option o una optgroup alla select. Normalmente restituisce l'oggetto select, se invece il parametro opzionale
         * flag è true restituisce un riferimento all'ultimo oggetto inserito per il chaining
         * @param mixed $option
         * @param bool $flag
         * @return mixed
         */
        public function addoption($option, $flag=false){
            if (($option instanceof Optgroup) || ($option instanceof Option)){
                $option->setContainer($this);
                $this->_options [] = $option;
                if ($flag){
                    return $this->_options[count($this->_options)-1];
                }
                return $this;
            }
        }
        

    }

}

?>