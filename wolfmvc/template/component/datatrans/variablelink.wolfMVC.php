<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Datatrans{
    
    class Variablelink extends Datatrans {
     
        /**
         * @readwrite
         * @var string
         */
        protected $_href;
        
        /**
         * @readwrite
         * @var array
         */
        protected $_params;
        
        /**
         * @readwrite
         * @var array
         */
        protected $_data;
        
        public function tr($input){
            $this->_params = explode(",", $this->_params);
            foreach ($this->_params as $key => $param){
                preg_match("/^{{.*}}/",$param,$matches);
                if (count($matches) == 1){
                    $this->_params[$key] = $this->_data[str_ireplace("{{", "", str_ireplace("}}", "", $param))];
                }
            }
            if (!empty($this->_href))
                return "<a href=\"" . vsprintf($this->_href, $this->_params) . "\">{$input}</a>";
            else {
                return $input . "<br>warning:: no href ";
            }
        }
    }
}