<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    class Label extends Formcomponent {

        /**
         * @readwrite
         * @var string
         */
        protected $_content;
        protected $_availableattributes = array();

        public function make($html) {
            $html .="<label>{$this->_content}</label>\n";
            return $html;
        }

    }

}

?>