<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Operation{
    
    
    /** operation per datadisplay
     * 
     */
    abstract class Fieldoperation extends \WolfMVC\Template\Component\Operation{
        
        
        /**
         * @readwrite
         * @var string
         */
//        protected $_name;
        
        /**
         * @readwrite
         * @var array
         */
//        protected $_params;
        
        /**
         * @readwrite
         * @var string
         */
//        protected $_opid;
        
        /**
         * @readwrite
         * @var string
         */
        protected $_idfield;
        
        /**
         * @readwrite
         * @var mixed
         */
        protected $_idvalue;
        
        /**
         * @readwrite
         * @var boolean
         */
        protected $_retrievedata = false;
        
        
        
        
    }
}

?>