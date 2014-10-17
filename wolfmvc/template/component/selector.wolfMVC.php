<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component {

    /**
     * Classe base dei selettori. Si tratta di componenti software che gestiscono l'operazione di selezione di un elemento da una lista.
     * Gli elementi sono record i cui campi sono le componenti. Certe componenti svolgono il ruolo di indice. Un indice può essere visibile o non visibile
     * e serve come parametro di ordinamento e/o come parametro da passare ad operazioni agganciate al selettore.
     */
    class Selector extends \WolfMVC\Base {

        /**
         * @var array Elenco delle componenti dell'oggetto del selettore
         * @read
         */
        protected $_components = array();

        /**
         * @var array Elenco dei nomi da visualizzare per le componenti
         * @readwrite
         */
        protected $_titles = array();

        /**
         *
         * @var array Elenco delle componenti dell'oggetto del selettore da considerare chiavi visibili
         */
        protected $_indexes = array();

        /**
         *
         * @var array Elenco delle componenti dell'oggetto del selettore da considerare chiavi invisibili
         */
        protected $_hiddenindexes = array();

        /**
         * @read
         * @var array Ordinamento scelto per il selettore
         */
        protected $_order = array();

        /**
         * @readwrite
         * @var array I dati degli oggetti del selettore. Deve essere un array uniforme di array con indici uguali al vettore $_components.
         */
        protected $_data = array();

        /**
         * @readwrite
         */
        protected $_htmlClass;

        /**
         * @readwrite
         */
        protected $_htmlId;

        public function setComponents($comps) {
            if (!is_array($comps))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
            $this->_components = $comps;
        }

        public function setIndexes($comps) {
            if (!is_array($comps))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
            $this->_indexes = $comps;
        }

        public function setHiddenIndexes($comps) {
            if (!is_array($comps))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
            $this->_hiddenindexes = $comps;
        }

        public function setData($data) {
            if (!is_array($data))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
            foreach ($data as $k => $v) {
                if (!is_array($v))
                {
                    throw new \WolfMVC\Core\Exception\Argument();
                }
                foreach ($this->_components as $kcomp => $comp) {
                    if (!(array_key_exists($comp, $v)))
                    {
                        throw new \WolfMVC\Core\Exception\Argument();
                    }
                }
                array_push($this->_data, $v);
            }
        }

        /**
         * permette di indicare un campo per l'ordine, la direzione ASC/DESC e il tipo di campo
         */
        public function setOrder($comp, $type = "string", $dir = "ASC") {
            if (!in_array($comp,$this->_components))
            {
                return;
            }
            if (!in_array($type, array("int", "string", "date")))
            {
                return;
            }
            $dir = strtolower($dir);
            if (!in_array($dir, array("asc", "desc")))
            {
                return;
            }
            $this->_order = array("comp" => $comp, "type" => $type, "dir" => $dir);
        }

        public function intSortAsc($a, $b) {
            return ((int) $a[$this->_order["comp"]] - (int) $b[$this->_order["comp"]]);
        }

        public function intSortDesc($a, $b) {
            return (-1) * self::intSortAsc($a, $b);
        }

        public function dateSortAsc($a, $b) {
            $a = new \DateTime($a[$this->_order["comp"]]);
            $b = new \DateTime($b[$this->_order["comp"]]);
            $interval = $a->diff($b);
            return $a->getTimestamp() - $b->getTimestamp();
        }

        public function dateSortDesc($a, $b) {
            return (-1) * self::dateSortAsc($a, $b);
        }

        public function stringSortAsc($a, $b) {
            return strcmp($a[$this->_order["comp"]], $b[$this->_order["comp"]]);
        }

        public function stringSortDesc($a, $b) {
            return (-1) * self::stringSortAsc($a, $b);
        }

        public function order() {
            $comp = $this->_order["comp"];
            $type = $this->_order["type"];
            $dir = $this->_order["dir"];
//            print_r($this->_order);
            switch ($type) {
                case "int":
                    ($dir === "asc") ? usort($this->_data, array("WolfMVC\Template\Component\Selector","intSortAsc")) : usort($this->_data, array("WolfMVC\Template\Component\Selector","intSortDesc"));
                    break;
                case "date":
                    ($dir === "asc") ? usort($this->_data, array("WolfMVC\Template\Component\Selector","dateSortAsc")) : usort($this->_data, array("WolfMVC\Template\Component\Selector","dateSortDesc"));
                    break;
                default:
                    ($dir === "asc") ? usort($this->_data, array("WolfMVC\Template\Component\Selector","stringSortAsc")) : usort($this->_data, array("WolfMVC\Template\Component\Selector","stringSortDesc"));
                    break;
            }
        }

        /**
         * @todo aggiungere eccezioni
         * Genera il template di un selettore
         */
        public function make($html) {
            
            if (isset($this->_order) && count($this->_order) === 3)
            {
                $this->order();
            }
            $html .= "<div";
            if (isset($this->_htmlClass))
            {
                $html .= " class=\"{$this->_htmlClass}\"";
            }
            if (isset($this->_htmlId))
            {
                $html .= " method=\"{$this->_htmlId}\"";
            }
            $html .= ">";
//            if (isset($this->_formlabel) && !(empty($this->_formlabel)))
//            {
//                $html .= " <fieldset>\n<legend>" . $this->_formlabel . "</legend>\n";
//            }
//            echo "<pre>";
//            print_r($this->_fields);
//            echo "</pre>";
            $html .= "{EMPTY SELECTOR}";
            $html .= "\n</div>";
            return $html;
        }

        public function includeScript() {
            
        }

    }

}

?>