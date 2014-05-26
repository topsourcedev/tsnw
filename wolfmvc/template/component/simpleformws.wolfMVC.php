<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component {

    /**
     * Estensione di {@see Simpleform} che aggiunge la funzionalità campo di ricerca.
     */
    class Simpleformws extends Simpleform {

        /**
         * @var array Gli indici dei campi di ricerca
         * @readwrite
         */
        protected $_searchfield;

        /**
         *
         * @var array Dati sulla suggestbox (width,height);
         * @readwrite
         */
        protected $_suggestboxsize = array();

        /**
         *
         * @var string L'url da cercare con segnaposto ###n###
         * @readwrite
         */
        protected $_searchurl = "";

        /**
         * @read
         * @var array
         */
        protected $_completingfields = array();

        /**
         * Genera il template di un form
         * @param string $html La stringa in cui inserire 
         * @todo Migliorare filtro sui campi che ora fa schifo!!!
         */
        public function make($html) {
            $fieldsarray = array();
            $searchfields = array();
            foreach ($this->_fields as $key => $field) {
                if (isset($field[1]) && ($field[1] == true)) {
                    $fieldsarray[] = $field[0]->getName();
                }
            }
            $fieldsarrayjs = \WolfMVC\ArrayMethods::toJSArray($fieldsarray);
            $suggestid = "a" . \WolfMVC\StringMethods::random_string(10);
            if (!((isset($this->_searchfield)))){
                return;
            }
            foreach ($this->_searchfield as $sf) {
                if (!is_int($sf) || ($sf < 0)) {
                    return;
                }
                $searchfields [] = $this->_fields[$sf][0]->getName();
            }
            $searchfieldsjs = \WolfMVC\ArrayMethods::toJSArray($searchfields);
            foreach ($this->_searchfield as $sf){
                $this->_fields[$sf][0]
                    ->setExternal(" onkeyup=\"showHint({$searchfieldsjs}, '{$suggestid}','{$this->_searchurl}',{$fieldsarrayjs})\"");
            }
            $html = parent::make($html);
            if (!isset($this->_suggestboxsize[0]) || empty($this->_suggestboxsize[0]) || ((int) ($this->_suggestboxsize[0]) == 0)) {
                $this->_suggestboxsize[0] = 40;
            }
            if (!isset($this->_suggestboxsize[1]) || empty($this->_suggestboxsize[1]) || ((int) ($this->_suggestboxsize[1]) == 0)) {
                $this->_suggestboxsize[1] = 60;
            }
            $html .= "<p>Suggerimenti: <span style=\"overflow-y: auto; overflow-x: "
              . "none; display: block; width:{$this->_suggestboxsize[0]}px; height:{$this->_suggestboxsize[1]}px;\" id=\"{$suggestid}\"></span></p>";
            return $html;
        }

        

        /**
         * Rende completing l'ultimo campo inserito se è applicabile
         * @return Simpleformws
         */
        public function setCompleting() {
            if (!empty($this->_fields[count($this->_fields) - 1])) {
                $this->_fields[count($this->_fields) - 1][1] = true;
            }
            return $this;
        }

    }

}

?>