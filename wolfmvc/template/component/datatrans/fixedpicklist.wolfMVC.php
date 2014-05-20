<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Datatrans{
    
    class Fixedpicklist extends Datatrans {
     
        /**
         * @readwrite
         * @var array
         */
        protected $_values;
        
        public function tr($input){
            if ((is_int($input) || (is_string($input) && (("".(int)($input))===$input))) && isset($this->_values[$input]))
                return $this->_values[$input];
            else{
                ob_start();
                echo "<pre>";
                print_r($this->_values);
                echo "</pre>";
                $contents = ob_get_contents();
                
                ob_end_clean();
                
                return $input.$contents;
                
            }
        }
    }
}