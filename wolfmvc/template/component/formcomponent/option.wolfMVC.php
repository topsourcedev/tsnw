<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /*
     * da fare
     */

    class Option extends Formcomponent {

        /**
         * @readwrite;
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
         * @var mixed
         */
        protected $_container;
        protected $_availableattributes = array("disabled", "selected", "value");

        public function make($html) {

            $html .= "<option";
            if (isset($this->_label) && (!empty($this->_label))) {
                $html .=" label=\"{$this->_label}\"";
            }
            $html = $this->attributes2html($html);
            $html .= ">";
            if (isset($this->_content) && (!empty($this->_content))) {
                $html .= $this->_content;
            }
            $html .= "</option>\n";
            return $html;
        }

        public function up() {
            if (isset($this->_container) && !empty($this->_container))
                return $this->_container;
            return null;
        }

    }

}

?>