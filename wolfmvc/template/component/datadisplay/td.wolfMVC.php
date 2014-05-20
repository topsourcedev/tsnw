<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Datadisplay {

    /**
     * Elemento td di html
     */
    class Td extends Datum {

        /**
         * @readwrite
         * @var string;
         */
        protected $_onclick;
        
        public function __construct($options = array()) {
            if ((!isset($options['id'])) || (!isset($options['class'])))
                echo 'errore!';
            parent::__construct($options);
        }
        
        public function make($html) {
            $html .= "<td";
            if ((isset($this->_class)) && (!empty($this->_class))){
                $html .=" class=\"{$this->_class}\"";
            }
            if ((isset($this->_id)) && (!empty($this->_id))){
                $html .=" id=\"{$this->_id}\"";
            }
            if ((isset($this->_onclick)) && (!empty($this->_onclick))){
                $html .=" ondblclick=\"{$this->_onclick}\"";
            }
            $html .= ">";
            if ($this->_trans){
                if (is_string($this->_trans)){
                    $this->_trans = new $this->_trans;
                }
                $html.= $this->_trans->tr($this->_datum);
            }
            else{
                $html .= $this->_datum;
            }
            $html .= "</td>\n";
            return $html;
        }

    }

}