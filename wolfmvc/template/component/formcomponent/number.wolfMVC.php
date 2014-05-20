<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /**
     * Corrisponde al tag input type="number"
     */
    class Number extends Text {
        
        /**
         * @readwrite;
         * @var string
         */
        protected $_label;
        protected $_type = "number";

        protected $_availableattributes = array("autocomplete","autofocus","disabled","max","maxlength","min","name","readonly","required","step","value");

        public function make($html) {
            return parent::make($html);
        }

    }

}

?>