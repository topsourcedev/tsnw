<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Selector {

    /**
     * Classe base dei selettori. Si tratta di componenti software che gestiscono l'operazione di selezione di un elemento da una lista.
     * Gli elementi sono record i cui campi sono le componenti. Certe componenti svolgono il ruolo di indice. Un indice può essere visibile o non visibile
     * e serve come parametro di ordinamento e/o come parametro da passare ad operazioni agganciate al selettore.
     */
    class Listone extends \WolfMVC\Template\Component\Selector {
        /**
         * @var array Elenco delle componenti dell'oggetto del selettore
         * @read
         */
//        protected $_components = array();

        /**
         *
         * @var array Elenco delle componenti dell'oggetto del selettore da considerare chiavi visibili
         */
//        protected $_indexes = array();

        /**
         *
         * @var array Elenco delle componenti dell'oggetto del selettore da considerare chiavi invisibili
         */
//        protected $_hiddenindexes = array();

        /**
         * @read
         * @var array Ordinamento scelto per il selettore
         */
//        protected $_order = array();

        /**
         * @readwrite
         * @var array I dati degli oggetti del selettore. Deve essere un array uniforme di array con indici uguali al vettore $_components.
         */
//        protected $_data = array();

        /**
         * @readwrite
         */
        protected $_htmlClass = "listone";

        /**
         *
         * @readwrite
         */
        protected $_link = "";

        /**
         * @readwrite
         */
        protected $_htmlId = "listone";
        protected $_renderParameters = array("itemsInAPage" => 10);

//        public function setComponents($comps) {
//            if (!is_array($comps))
//            {
//                throw new \WolfMVC\Core\Exception\Argument();
//            }
//            $this->_components = $comps;
//        }
//
//        public function setIndexes($comps) {
//            if (!is_array($comps))
//            {
//                throw new \WolfMVC\Core\Exception\Argument();
//            }
//            $this->_indexes = $comps;
//        }
//
//        public function setHiddenIndexes($comps) {
//            if (!is_array($comps))
//            {
//                throw new \WolfMVC\Core\Exception\Argument();
//            }
//            $this->_hiddenindexes = $comps;
//        }
//
//        public function setData($data) {
//            if (!is_array($data))
//            {
//                throw new \WolfMVC\Core\Exception\Argument();
//            }
//            foreach ($data as $k => $v) {
//                if (!is_array($v))
//                {
//                    throw new \WolfMVC\Core\Exception\Argument();
//                }
//                foreach ($this->_components as $kcomp => $comp) {
//                    if (!(array_key_exists($comp, $v)))
//                    {
//                        throw new \WolfMVC\Core\Exception\Argument();
//                    }
//                }
//                array_push($this->_data, $v);
//            }
//        }
//        public function setOrder() {
//            
//        }
//
//        public function order(){
//            
//        }

        public function includeScript() {
            $inc = "";
            $inc .="<script type=\"text/javascript\" src=\"" . SITE_PATH . "js/component/listone.js\"></script>";
            return $inc;
        }

        
        public function ottimizzalarghezzacolonne(){
            $largh = array();
            $larghperc = array();
//            print_r($this->_components);
            
            foreach ($this->_components as $compk => $comp){
            
                $largh[$comp] = 0;
            }
            
            foreach ($this->_data as $datak => $data){
                foreach ($this->_components as $compk => $comp){
                    $len = strlen($comp);
                    if ($len > $largh[$comp]){
                        $largh[$comp] = $len;
                    }
                }
            }
            
            $sum = 0;
            foreach ($largh as $k => $l){
                $sum += $l;
            }
            foreach ($largh as $k => $l){
                $larghperc[$k] = floor(100*$l/$sum);
            }
            return $larghperc;
        }
        
        /**
         * @todo aggiungere eccezioni
         * Genera il template di un selettore
         */
        public function make($html) {
            $out = parent::make($html);
            $html .= "<div style=\"width: 100%; height: 100%\"";
            $largperc = $this->ottimizzalarghezzacolonne();
            if (isset($this->_htmlClass))
            {
                $html .= " class=\"{$this->_htmlClass}\"";
            }
            if (isset($this->_htmlId))
            {
                $html .= " id=\"{$this->_htmlId}\"";
            }
            $html .= ">";
            $numpages = floor((count($this->_data) - 1) / $this->_renderParameters["itemsInAPage"] + 1);
            $j = 0;

            for ($page = 0; $page < $numpages; $page++) {
                $html .= "<div class=\"pageNav\" id=\"pageNav_{$page}\"";
                if ($page > 0)
                    $html.= " style=\"display: none;\"";
                $html .=">";
                if ($page > 0)
                    $html .= "<span onclick=\"listone_back({$page},this)\">&lt;</span>&nbsp;";
                $html .=($page + 1) . "/{$numpages}";
                if ($page < $numpages - 1)
                    $html .= "&nbsp;<span onclick=\"listone_forw({$page},this)\">&gt;</span>";
                $html .= "</div>\n<div";
                if (isset($this->_htmlClass))
                {
                    $html .= " class=\"{$this->_htmlClass}_page\"";
                }
                if (isset($this->_htmlId))
                {
                    $html .= " id=\"{$this->_htmlId}_page_{$page}\"";
                }
                if ($page > 0)
                    $html .= " style=\"display: none; width: 100%; height: 100%\"";
                $html .= " style=\"width: 100%; height: 100%\"";
                $html .= "><br><br>";
                $html .= "<table style=\"width: 100%; height: 100%\"";
                if (isset($this->_htmlClass))
                {
                    $html .= " class=\"{$this->_htmlClass}\"";
                }
                if (isset($this->_htmlId))
                {
                    $html .= " id=\"{$this->_htmlId}_table\"";
                }
                $html .= "><tr>";
                foreach ($this->_components as $compk => $comp) {
                    if (isset($this->_titles[$comp]))
                        $html .= "<th width=\"{$largperc[$comp]}%\">{$this->_titles[$comp]}</th>";
                    else
                        $html .= "<th width=\"{$largperc[$comp]}%\">$comp</th>";
                }
                $html .= "</tr>";
                for ($i = 0; $i < $this->_renderParameters["itemsInAPage"]; $i++) {
                    if (!isset($this->_data[$j]))
                        break;
//                    echo "j=".$j."<br>";
                    $html .= "<tr";
                    $link = $this->_link;
                    foreach ($this->_indexes as $indexk => $index) {
                        if (isset($this->_data[$j][$index]))
                        {
                            $link = str_ireplace("{{{index_{$indexk}}}}", $this->_data[$j][$index], $link);
                        }
                    }
                    if ($link != "")
                    {
                        $html.= " onclick=\"window.location.href = '{$link}'\"";
                    }
                    $html .= ">";
                    foreach ($this->_components as $compk => $comp) {
                        $html .= "<td>{$this->_data[$j][$comp]}</td>";
                    }
                    $html .= "</tr>";
                    $j++;
                }
                $html .= "</table>";
                $html .= "\n</div>";
            }


            $html = str_ireplace("{EMPTY SELECTOR}", $html, $out);
            ;
            $html .= "\n</div>";
            return $html;
        }

    }

}

?>