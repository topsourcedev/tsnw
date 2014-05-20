<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /*
     * da fare
     */

    class Radio extends Text {

        /**
         * @readwrite;
         * @var string
         */
        protected $_label;

        /**
         * @read
         * @var string
         */
        protected $_type = "radio";
        protected $_availableattributes = array("autofocus", "checked", "disabled", "name", "required", "value");

        public function make($html) {

            $html .= "<input type=\"" . $this->_type . "\"";
            $html = $this->attributes2html($html);
            $html .= ">";
            if (isset($this->_label) && (!empty($this->_label)) && (!empty($this->_name))) {
                $html .= $this->_label;
            }
            return $html;
        }

    }

}

?>