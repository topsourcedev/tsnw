<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template {


    abstract class Pagecomponent extends \WolfMVC\Base {

        /**
         *
         * @readwrite
         */
        protected $_sizeInfo;
        
        /**
         *
         * @readwrite
         */
        protected $_data;
       
         /**
         *
         * @readwrite
         */
        protected $_labels;
        
        /**
         * @readwrite
         * @var array
         */
        protected $_titleInfo;
        
        public function render($html) {
            return "<div class=\"pageComponent\">".$html."<div>";
        }
        abstract public function renderTitle();
        abstract public function includingScript();
        abstract public function includingCss();
    }
}
?>