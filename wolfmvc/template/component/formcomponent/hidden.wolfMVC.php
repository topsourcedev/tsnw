<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /**
     * Corrisponde al tag input type="hidden"
     */
    class Hidden extends Text {
        
        /**
         * @readwrite;
         * @var string
         */
        protected $_label;
        protected $_type = "hidden";

        protected $_availableattributes = array("name","value");

        public function make($html) {
            
            $html .= "<input type=\"" . $this->_type . "\"";
            $html = $this->attributes2html($html);
            $html .= ">";
            return $html;
        }

    }

}

?>