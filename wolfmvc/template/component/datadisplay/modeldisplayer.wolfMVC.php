<?php

namespace WolfMVC\Template\Component\Datadisplay {

    use WolfMVC\Base as Base;
use WolfMVC\ArrayMethods as ArrayMethods;
use \WolfMVC\Template\Component as Comp;

    class Modeldisplayer extends Comp\Datadisplay {

        /**
         * @readwrite
         * @var string
         */
        protected $_table = "";
        protected $_columns = array(
          "amount" => "Ammontare",
          "type" => "Tipo",
          "accountid" => "Cliente",
          "state" => "Stato",
          "ref" => "Riferimento",
          "bankid" => "Banca",
          "emissiondate" => "Data di emissione",
          "ourbankid" => "Nostra banca"
        );

        public function initialize() {
//            echo method_exists($this->_model, "all");
//            echo "\\".$this->_model."::all(array(), array('*'), null, null, null, null, '".$this->_deviate_table."');";
            eval("$" . "this->_data = \\" . $this->_model . "::all(array(), array('*'), null, null, null, null, '" . $this->_table . "');");
        }

        public function make($html) {
            return $this->maketable($html);
        }
        
        public function maketable($html) {
            $this->initialize();
//            echo "<pre>";
//            print_r($this->_data);
//            echo "</pre>";
//            foreach ($this->_data as $d) {
//                $html .= $d->basic_show() . "<br>";
//                
//            }
            $html .= "<table width=\"100%\">\n";
            $html .= "<tr>";
            foreach ($this->_columns as $key => $col) {
                $html .= "<th>{$col}</th>";
            }
            $html .= "</tr>\n";
            foreach ($this->_data as $d) {
                $html .= "<tr>";
                foreach ($this->_columns as $key => $col) {
                    $html .= "<td>{$d->$key}</td>";
                }
                $html .= "</tr>";
            }
            $html .= "</table>";
            return $html;
        }

    }

}