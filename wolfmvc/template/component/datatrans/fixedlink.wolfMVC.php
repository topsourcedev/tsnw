<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Datatrans{
    
    class Fixedlink extends Datatrans {
     
        /**
         * @readwrite
         * @var array
         */
        protected $_href;
        
        public function tr($input){
            if (!empty($this->_href))
                return "<a href=\"{$this->_href}\">{$input}</a>";
            else{
                return $input."<br>warning:: no href ";
                
            }
        }
    }
}