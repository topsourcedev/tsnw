<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component\Formcomponent {

    /**
     * Classe astratta base di tutti i form component.<br>
     * I suoi attributi corrispondono agli attributi dei tag html interni ad un form. Presenta il metodo
     * astratto make($html) che scrive il codice html del campo e il metodo non astratto attributes2html($html)
     * che compila gli attributi applicabili al tag in questione.
     */
    abstract class Formcomponent extends \WolfMVC\Base {

        /**
         * @readwrite
         * @var bool
         */
        protected $_autocomplete;

        /**
         * @readwrite
         * @var bool
         */
        protected $_autofocus;

        /**
         * @readwrite
         * @var bool
         */
        protected $_checked;

        /**
         * @readwrite
         * @var int
         */
        protected $_cols;

        /**
         * @readwrite
         * @var bool Usare $comp->setDisabled(true) per rendere disabilitato il controllo. <br>Default=false. <br>Se vuoto viene ignorato.
         */
        protected $_disabled;

        /**
         * @readwrite
         * @var string
         */
        protected $_external ="";
        /**
         * @var int
         * @readwrite
         */
        protected $_max;

        /**
         * @var int
         * @readwrite
         */
        protected $_maxlength;

        /**
         * @var int
         * @readwrite
         */
        protected $_min;

        /**
         * @var string Indicare il nome del controllo<br>Default: stringa vuota<br>Deve essere valorizzato, perchè abbia senso.<br>
         * L'id e il name del controllo sono uguali a questo valore.
         * @readwrite
         */
        protected $_name;

        /**
         * @readwrite
         * @var bool
         */
        protected $_placeholder;

        /**
         * @readwrite
         * @var bool
         */
        protected $_readonly;

        /**
         * @readwrite
         * @var bool
         */
        protected $_required;

        /**
         * @readwrite
         * @var int
         */
        protected $_rows;

        /**
         * @readwrite
         * @var bool
         */
        protected $_selected;

        /**
         * @var int Indicare la dimensione in pixel del controllo. <br>Se vuoto o non applicabile viene ignorato.
         * @readwrite
         */
        protected $_size;

        /**
         * @var float Indicare la dimensione in pixel del controllo. <br>Se vuoto o non applicabile viene ignorato.
         * @readwrite
         */
        protected $_step;

        /**
         * @var string Indicare il valore iniziale del controllo. <br>Default=stringa vuota.<br> Se vuoto viene ignorato.
         * @readwrite
         */
        protected $_value;

        /**
         * @var string Indicare la classe del controllo<br>Default: stringa vuota<br>Se vuoto viene ignorato.
         * @readwrite
         */
        protected $_class;

        /**
         * @read
         * @var array Elenca gli attributi applicabili per una classe di controllo. Non è modificabile dall'esterno.
         */
        protected $_availableattributes = array();

        /**
         * 
         * @param string $html
         */
        public function attributes2html($html) {
            foreach ($this->_availableattributes as $attr) {
                $_attr = "_" . $attr;
                if ((property_exists(get_class($this), $_attr) !== false) && (isset($this->{$_attr})) && ((!empty($this->{$_attr})) || ($this->{$_attr} === false))) {
                    switch ($attr) {
                        case "name":
                        case "id":
                            $html .= " name=\"" . $this->$_attr . "\"";
                            $html .= " id=\"" . $this->$_attr . "\"";
                            break;
                        case "autocomplete":
                            $html .= " " . $attr . "=\"" . ($this->$_attr ? "on" : "off") . "\"";
                            break;
                        case "autofocus":
                        case "disabled":
                        case "readonly":
                        case "required":
                        case "checked":
                        case "selected":
                            $html .= $this->$_attr ? " " . $attr : "";
                            break;
                        default:
                            $html .= " " . $attr . "=\"" . $this->$_attr . "\"";
                    }
                }
            }
//            if ((isset($this->_external)) && (!empty($this->external))){
            if ((isset($this->_external))){
                $html .= " " . $this->_external;
            }
            return $html;
        }

        public abstract function make($html);
    }

}

?>