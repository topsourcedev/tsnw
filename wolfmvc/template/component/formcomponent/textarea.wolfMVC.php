<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {
    /*
     * da fare
     */

    class Textarea extends Formcomponent {

        /**
         * @readwrite
         * @var string
         */
        protected $_content;

        /**
         * @readwrite;
         * @var string
         */
        protected $_label;
        protected $_availableattributes = array("autofocus", "cols", "disabled", "maxlength", "name", "placeholder", "readonly", "required", "rows");

        public function make($html) {
            if (isset($this->_label) && (!empty($this->_label)) && (!empty($this->_name))) {
                $html .="<label for=\"{$this->_name}\">{$this->_label}</label>\n";
            }
            $html .= "<textarea";
            $html = $this->attributes2html($html);
            $html .= ">";
            if (isset($this->_content) && (!empty($this->_content))){
                $html .= $this->_content;
            }
            $html .= "</textarea>";
                return $html;
        }

    }

}

?>