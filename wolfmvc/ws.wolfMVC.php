<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {

    /**
     * Base Web Service
     */
    class WS extends Base {

        /**
         * @var array
         */
        protected $_ops = array(
          "describe" => array(
            "params" => array(),
            "returnmap" => array("mixed")
          )
        );

        public function addOp($opname) {
            $opname = strtolower($opname);
            if ((!isset($this->_ops[$opname])) || (!is_array($this->_ops[$opname]))) {
                $this->_ops[$opname] = array();
            }
            return $this;
        }

        public function describe() {
//            return json_encode($this->_ops);
            return ($this->_ops);
        }

    }

}