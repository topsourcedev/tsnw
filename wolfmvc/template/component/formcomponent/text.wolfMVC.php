<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    class Text extends Formcomponent {

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
        protected $_availableattributes = array("autocomplete", "autofocus", "disabled", "name", "placeholder", "readonly", "required", "size", "value");

        public function make($html) {
            if (isset($this->_label) && (!empty($this->_label)) && (!empty($this->_name))) {
                $html .="<label for=\"{$this->_name}\">{$this->_label}</label>\n";
            }
            $html .= "<input type=\"" . $this->_type . "\"";
            $html = $this->attributes2html($html);
            $html .= ">";
            return $html;
        }

    }

}

?>