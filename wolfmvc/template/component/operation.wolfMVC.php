<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component{
    
    
    /** operation per datadisplay
     * 
     */
    abstract class Operation extends \WolfMVC\Base{
        
        
        /**
         * @readwrite
         * @var string
         */
        protected $_name;
        
        /**
         * @readwrite
         * @var array
         */
        protected $_params;
        
        /**
         * @readwrite
         * @var string
         */
        protected $_opid;
        
        /**
         * @readwrite
         * @var string
         */
        protected $_elemid;
        
//        public function __construct($opid,$elemid) {
//            $this->_opid = $opid;
//            $this->_elemid = $elemid;
//            parent::__construct($options);
//        }
        
        public abstract function interf($input);
        
        public function describe(){
            return "Op".  get_class($this);
        }
    }
}

?>