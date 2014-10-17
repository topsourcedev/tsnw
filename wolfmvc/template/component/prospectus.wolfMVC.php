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
    class Prospectus extends \WolfMVC\Base {

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
         * @readwrite
         * @var array I dati degli oggetti del selettore. Deve essere un array uniforme di array con indici uguali al vettore $_components.
         */
        protected $_data = array();

        /**
         * @readwrite
         */
        protected $_htmlClass = "prospectus";

        /**
         * @readwrite
         */
        protected $_htmlId = "prospectus";

        /**
         * @readwrite
         * @var array
         */
        protected $_arrangement;
        
        public function setComponents($comps) {
            if (!is_array($comps))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
            $this->_components = $comps;
        }

        

        public function setData($data) {
            if (!is_array($data))
            {
                throw new \WolfMVC\Core\Exception\Argument();
            }
                if (!is_array($data))
                {
                    throw new \WolfMVC\Core\Exception\Argument();
                }
                foreach ($this->_components as $kcomp => $comp) {
                    if (!(array_key_exists($comp, $data)))
                    {
                        throw new \WolfMVC\Core\Exception\Argument();
                    }
                }
                $this->_data = $data;
        }


        /**
         * @todo aggiungere eccezioni
         * Genera il template di un selettore
         */
        public function make($html) {
//            print_r($this->_data);
            $html .= "<div";
            if (isset($this->_htmlClass))
            {
                $html .= " class=\"{$this->_htmlClass}\"";
            }
            if (isset($this->_htmlId))
            {
                $html .= " method=\"{$this->_htmlId}\"";
            }
            $html .= "style=\"text-align: left; padding: 10px;\">";
            
            foreach ($this->_arrangement as $rowk => $row){
                $html .= "<div style=\"width: 100%; font-size: 38pxdisplay: block;\" class=\"{$this->_htmlClass}_row_{$rowk}\">";
                $text = $row;
                foreach ($this->_components as $compk => $comp){
                    $text = str_replace("{{".$comp.":__label__}}", "<span>".$comp."</span>", $text);
                    $text = str_replace("{{".$comp."}}", "<span style=\"font-weight: bold;\">".$this->_data[$comp]."</span>", $text);
                }
                $html .= "<span>{$text}</span>";
                $html .= "</div>";
            }
            $html .= "\n</div>";
            return $html;
        }
        
        
        
        public function includeScript(){
            
        }

    }

}

?>