<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component {

    /**
     * Un oggetto di questa classe è in grado di gestire un vettore di vettori. 
     * @todo Manuale d'uso
     */
    class Paginator extends \WolfMVC\Base {


        /**
         * @readwrite
         * @var int Le soglie da usare
         */
        protected $_threshold;

        /**
         * @read
         * @var mixed Indice attuale
         */
        protected $_actualIndex = 0;

        /**
         * @readwrite
         * @var array I dati gestiti
         */
        protected $_data;

        /**
         * @read
         * @var bool Controlla che il paginatore sia pronto per essere usato
         */
        protected $_isInitialized = true;

        public function hasNext() {
            if (!$this->_isInitialized)
            {
                return false;
            }
            return (count($this->_data) > $this->_actualIndex + 1);
        }

        public function next() {
            if (!$this->_isInitialized)
            {
                throw new \WolfMVC\Template\Exception\Implementation();
            }
            if (!$this->hasNext())
                throw new \WolfMVC\Template\Exception\Implementation();
            return $this->_data[++$this->_actualIndex];
        }

        public function make($in) {
            //prendo tutti i dati e itero l'analisi sugli indici
            if ($count === -1){
                $count = count($this->_data);
            }
            $string = $in;
            $out = $in;
            $matches = array();
            preg_match("/{{REPEAT_($index)}}(.*){{REPEAT_($index)}}/i", $string, $matches);
            if (count($matches) !== 2)
            {
                return $out;
            }
            else {
                $repeating = $matches[1];
                $times = $count / $this->_thresholds[$this->_indexes[$index]];
                for ($indexval = 0; $indexval < $times; $indexval++){
                    $repeated = $repeating;
                    $internal = str_ireplace("{{REPEAT_($index)}}", "", $repeating)
                    $repeated = str_ireplace($repeated, $this->make($in), $repeated)
                }
            }
            foreach()
        }

    }

}

?>