<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /*
     * da fare
     */

    class Checkbox extends Text {

        /**
         * @readwrite;
         * @var string
         */
        protected $_label;

        /**
         * @read
         * @var string
         */
        protected $_type = "checkbox";
        protected $_availableattributes = array("autofocus", "checked", "disabled", "name", "required", "value");

        public function make($html) {

            return parent::make($html);
        }

    }

}

?>