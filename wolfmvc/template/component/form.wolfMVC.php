<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component {

    use WolfMVC\Template\Component\Formcomponent as FC;

    abstract class Form extends \WolfMVC\Base {

        /**
         * E' un array di array. In ogni posizione il primo elemento è un oggetto di classe Formcomponent o derivate.
         * I successivi elementi contengono indicazioni aggiuntive utile per le sottoclassi.
         * @readwrite
         */
        protected $_fields = array();

        /**
         * @readwrite
         */
        protected $_method;

        /**
         * @readwrite
         */
        protected $_action;

        /**
         * @var string Eventuale html esterno da includere nel form, utile per cuciture
         * @readwrite
         */
        protected $_append;

        /**
         * @var string Se non vuoto il form viene gestito all'interno di un fieldset con questa label.
         * @readwrite
         */
        protected $_formlabel;

        /**
         * @var string Opzionale. Nome interno del form.
         * @readwrite
         */
        protected $_formname;
        
        /**
         * 
         */
        
        /**
         * @readwrite
         * @var array
         */
        protected $_sensiblefields = array();

        public function setSensible() {
            if (!empty($this->_fields[count($this->_fields) - 1])) {
                $this->_fields[count($this->_fields) - 1]['sensible'] = true;
            }
            return $this;
        }
        
        public function getSensibleFields() {
            $sens = array();
            foreach($this->_fields as $key => $field){
                if ((isset($field['sensible'])) && ($field['sensible'] === true)){
                    $sens[] = $key;
                }
            }
            return $sens;
        }

        /**
         * Genera il template di un form
         */
        public abstract function make($html);

        /**
         * Aggiunge un componente al form. Se $flag è true restituisce un riferimento a questo componente 
         * altrimenti restituisce il form.
         * @param mixed $fc
         * @param bool $flag
         */
        public function add($fc, $flag = false) {
            if ($fc === "br") {
                $this->_fields [] = array();
                if ($flag)
                    return $this->_fields[count($this->_fields) - 1][0];
                return $this;
            }
            elseif (is_a($fc, "\\WolfMVC\\Template\\Component\\Formcomponent\\Formcomponent")) {
                $this->_fields [] = array($fc);
                if ($flag)
                    return $this->_fields[count($this->_fields) - 1][0];
                return $this;
            }
            else {
                echo "errore " . "WolfMVC\\Template\\Component\\Formcomponent\\" . $fc . " non e di tipo WolfMVC\\Template\\Component\\Formcomponent";
//                echo "<pre>";
//                print_r(class_parents("WolfMVC\\Template\\Component\\Formcomponent\\" . $fc));
//                echo "</pre>";
            }
        }

    }

}

?>