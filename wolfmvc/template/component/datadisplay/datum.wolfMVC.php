<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Datadisplay {

    /**
     * Gestisce la visualizzazione di un singolo dato.
     * 
     */
    abstract class Datum extends \WolfMVC\Base {

        /**
         * @readwrite
         * @var mixed Il dato da rappresentare
         */
        protected $_datum;

        /**
         * @readwrite
         * @var string
         */
        protected $_class;

        /**
         * @readwrite
         * @var string
         */
        protected $_id;
        /**
         * @readwrite
         * @var string
         */
        protected $_trans = null;

        
        public abstract function make($html);
    }

}