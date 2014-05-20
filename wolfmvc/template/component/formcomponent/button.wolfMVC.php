<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /*
     * da fare
     */
    class Button extends Formcomponent {
        
        /**
         * @readwrite
         * @var string
         */
        protected $_label;

        /**
         * @readwrite
         * @var string
         */
        protected $_content;
        /**
         * @readwrite
         * @var string
         */
        protected $_type;
        protected $_availableattributes = array("autofocus","disabled","name","value");

        public function make($html) {
            
            $html .= "<button type=\"{$this->_type}\"";
            $html = $this->attributes2html($html);
            $html .= ">";
            $html .= $this->_content;
            $html .= "</button>";
            return $html;
        }

    }

}

?>