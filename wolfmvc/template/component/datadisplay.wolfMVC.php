<?php

namespace WolfMVC\Template\Component {

    use WolfMVC\Base as Base;
    use WolfMVC\ArrayMethods as ArrayMethods;

    abstract class Datadisplay extends Base {

        /**
         * @readwrite
         * @var mixed
         */
        protected $_model;

        /**
         * @readwrite
         * @var array
         */
        protected $_data = array();
        
        /**
         * @readwrite
         * @var string
         */
        protected $_source;

        public function __construct($model) {
            if (!isset($model) || empty($model)){
                echo "errore! modello vuoto";
                return;
            }
            if (!(($model instanceof \WolfMVC\Multimodel))){
                echo "errore! modello non valido";
                return;
            }
            $this->_model = $model;
            parent::__construct();
        }
        
        
        public function describe($details = "") {
            echo "*********************<br>";
            echo "Sono un " . get_class($this) . " con le seguenti caratteristiche:<br>";
            echo $details;
            echo "Model: {".get_class($this->_model)."}";
            $this->_model->describe();
            echo "*********************<br>";
        }

        public abstract function getDataFromModel();

        public abstract function make($html);

    }

}