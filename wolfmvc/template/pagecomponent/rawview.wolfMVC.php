<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Pagecomponent {


    class Rawview extends \WolfMVC\Template\Pagecomponent {

        /**
         *
         * @readwrite
         */
        protected $_sizeInfo = array("w" => "0", "h" => "0");

        /**
         *
         * @readwrite
         */
        protected $_data = array();
        
        /**
         *
         * @readwrite
         */
        protected $_labels = array();

        public function render($html) {
            ob_start();
            echo "<div style=\"overflow: scroll; width: ".$this->_sizeInfo["w"]."px; height: ".$this->_sizeInfo["h"]."px\">";
            echo "<table>";
            echo "<tr>";
            foreach ($this->_labels as $key => $col) {
                    echo "<th>".$col."</th>";
                }
            echo "</tr>";
            foreach ($this->_data as $rownumber => $row) {
                echo "<tr>";
                foreach ($this->_labels as $key => $col) {
                    echo "<td>".$row[$col]."</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
            echo "<div>";
            $out = ob_get_contents();
            ob_end_clean();
            return parent::render($out);
        }

        public function includingScript() {
            
        }

    }

}
?>