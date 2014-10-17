<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template {


    abstract class Pagecomponentelement extends \WolfMVC\Base {

        /**
         *
         * @readwrite
         */
        protected $_datum;

        /**
         *
         * @var readwrite
         */
        protected $_parameters = array();


        public function render($html) {
            return "<div class=\"pageComponentElement\">" . $html . "<div>";
        }

        public function setParameter($key, $value) {
            if (isset($this->_parameters[$key]))
                $this->_parameters[$key] = $value;
        }

    }

}
?>