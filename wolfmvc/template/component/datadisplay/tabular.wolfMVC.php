<?php

namespace WolfMVC\Template\Component\Datadisplay {

    use WolfMVC\Base as Base;
use WolfMVC\ArrayMethods as ArrayMethods;
use \WolfMVC\Template\Component as Comp;

    class Tabular extends Comp\Datadisplay {

        /**
         * @readwrite
         * @var string
         */
        protected $_id = "tabular";

        /**
         * @readwrite
         * @var string
         */
        protected $_table = "";

        /**
         * @readwrite
         * @var array
         */
        protected $_columns = array();
        protected $_rendercommands = array();

        /**
         * 
         * @var boolean
         */
        protected $_indexfrommodel = true;

        /**
         * 
         * @var string
         */
        protected $_indexinmodel = "";

        /**
         * 
         * @var boolean
         */
        protected $_displayindex = false;

        /**
         *
         * @var array
         */
        protected $_operations = array();
        
        /**
         *
         * @var array
         */
        protected $_recoperations = array();

        /**
         * @readwrite
         * @var string
         */
        protected $_searchurl;

        /**
         * @readwrite
         * @var string
         */
        protected $_editurl;
        
        /**
         * @readwrite
         * @var string
         */
        protected $_deleteurl;
        
        /**
         * @readwrite
         * @var array
         */
        protected $_servicesforrecordop = array();

        public function __construct($model, $id = "") {
            if ($id != "") {
                $this->_id = $id;
            }
            parent::__construct($model);
        }

        public function addCol($name, $name_in_model) {
            if (!(is_string($name))) {
                return;
            }
            $this->_columns [$name_in_model] = $name;
            return $this;
        }

        public function showIndex($show) {
            if (is_bool($show)) {
                $this->_displayindex = $show;
            }
            return $this;
        }

        public function setIndexFromModel($setIndexFromModel, $index = "") {
            if (is_bool($setIndexFromModel)) {
                if ($setIndexFromModel && ($index != "") && is_string($index)) {
                    $this->_indexfrommodel = true;
                    $this->_indexinmodel = $index;
                }
                else {
                    $this->_indexfrommodel = false;
                    $this->_indexinmodel = "";
                }
            }
            return $this;
        }

        public function make($html) {
            return $this->maketable($html);
        }

        public function maketable($html) {

            $index = 0;
            $html .= "<table border=\"1\" width=\"100%\" id=\"{$this->_id}\" class=\"tabular\">\n";
            $html .= "<tr>";
            if ($this->_displayindex) {
                $html .= "<th>#</th>";
            }
            foreach ($this->_columns as $key => $col) {
                $html .= "<th>{$col}</th>";
            }
            $html .= "</tr>\n";
            foreach ($this->_data as $d) {

                $index++;
                $html .= "<tr>";
                if ($this->_displayindex) {
                    if ($this->_indexfrommodel) {
                        $html .= "<td>" . $d[$this->_indexinmodel] . "</td>";
                    }
                    else {
                        $html .= "<td>" . $index . "</td>";
                    }
                }
                foreach ($this->_columns as $key => $col) {
                    if ($this->_indexfrommodel) {
                        $tdid = $this->_id . "_" . $key . "_" . $d[$this->_indexinmodel];
                    }
                    else {
                        $tdid = $this->_id . "_" . $key . "_" . $index;
                    }

                    $tdclass = "tabular";
                    $td = new Td(array(
                      "id" => $tdid,
                      "class" => $tdclass
                    ));
                    if (isset($this->_rendercommands[$key])) {
                        switch ($this->_rendercommands[$key][0]) {
                            case 'fixedpicklist':
                                if (isset($this->_rendercommands[$key][1]) && (is_array($this->_rendercommands[$key][1]))) {
                                    $values = $this->_rendercommands[$key][1];
                                    $td->setTrans(new Comp\Datatrans\Fixedpicklist(array("values" => $values)));
                                }
                                break;
                            case 'fixedlink':
                                if (isset($this->_rendercommands[$key][1]) && (is_string($this->_rendercommands[$key][1]))) {
                                    $td->setTrans(new Comp\Datatrans\Fixedlink(array("href" => $this->_rendercommands[$key][1])));
                                }
                                break;
                            case 'variablelink':
                                if (isset($this->_rendercommands[$key][1]) && (is_array($this->_rendercommands[$key][1]))) {
                                    $href = $this->_rendercommands[$key][1][0];
                                    $params = $this->_rendercommands[$key][1][1];
                                    $td->setTrans(new Comp\Datatrans\Variablelink(array("href" => $href, "params" => $params, "data" => $d)));
                                }
                                break;
                        }
                    }
                    $datum = $d[$col] ? $d[$col] : "";
                    if (isset($this->_operations[$key])) {
                        switch ($this->_operations[$key]['op']) {
                            case 'edit':
                                $td->setOnclick("editfield(0, '{$tdid}', " . ($this->_operations[$key]['ispicklist'] ? 'true' : 'false') . ", '{$this->_operations[$key]['type']}', '" . (($this->_operations[$key]['ispicklist']) ? $this->_searchurl : "") . "', '" . (($this->_operations[$key]['ispicklist']) ? $this->_operations[$key]['secondaryid'] : "") . "', '{$this->_editurl}', '{$this->_operations[$key]['idop']}','" . (($this->_indexfrommodel) ? $d[$this->_indexinmodel] : $index) . "')");
                                break;
                            case 'editwithrestriction':
                                $restriction = $this->_operations[$key]['restriction'];
                                $res = array();
                                $include = "";
                                foreach ($restriction as $part => $value) {
                                    foreach ($value as $arr => $regole) {
                                        $array = array();
                                        
                                        foreach ($regole as $regola) {
                                            preg_match("/{{(.*)}}/", $regola, $matches);
                                            if (count($matches) > 0) {
//                                                print_r($matches);
                                                if (isset($this->_columns[$matches[1]])){
                                                    if ($this->_indexfrommodel) {
                                                        $id = $this->_id . "_" . $matches[1] . "_" . $d[$this->_indexinmodel];
                                                    }
                                                    else {
                                                        $id = $this->_id . "_" . $matches[1] . "_" . $index;
                                                    }
                                                }
                                                $array [] =  "(".str_ireplace($matches[0], "document.getElementById('{$id}').innerHTML.trim()", $regola).")";
                                            }
                                            else $array [] =  $regola;
                                        }
                                     $res[$part][$arr] = "\t\t\tcase {$arr}:\n\t\t\tcase '{$arr}':\n \t\t\t\treturn (".join(" && ",$array).");\n \t\t\tbreak;\n";
//                                     $res[$part][$arr] = str_ireplace("()", "false", $res[$part][$arr]);
                                     if ($res[$part][$arr] == "return ();")
                                         $res[$part][$arr] = 'return false;';
                                    }
                                    $res[$part] = "\tcase {$part}:\n\tcase '{$part}':\n\t\tswitch(arr){\n".join("\n",$res[$part])."\n \t\t\tdefault:\n \t\t\t\treturn false;\n\t\t}\n\t\tbreak;\n";
                                }
                                $include .= "function {$tdid}_check(part,arr){\nswitch(part){\n".join("\n",$res)."\n\tdefault:\n \t\treturn false;\n\t}\n}\n";
                              $html .= "<script>\n";
                              $html .= $include;
                              $html .= "</script>\n";
//                                  echo "<pre>";
//                                  echo $include;
//                                print_r($res);
//                                echo "</pre>";
                                $td->setOnclick("editfieldwithrestriction(0, '{$tdid}', " . ($this->_operations[$key]['ispicklist'] ? 'true' : 'false') . ", '{$this->_operations[$key]['type']}', '" . (($this->_operations[$key]['ispicklist']) ? $this->_searchurl : "") . "', '" . (($this->_operations[$key]['ispicklist']) ? $this->_operations[$key]['secondaryid'] : "") . "', '{$this->_editurl}', '{$this->_operations[$key]['idop']}','" . (($this->_indexfrommodel) ? $d[$this->_indexinmodel] : $index) . "')");
                                break;
                        }
//                        print_r($this->_operations[$key]);
                        //$td->setOnclick("");
                    }
                    $td->setDatum($datum)->setClass("tabular");
                    $html .= $td->make("");
                }
                foreach ($this->_recoperations as $key => $op){
                    if ($this->_indexfrommodel) {
                        $tdid = $this->_id . "_" . $key . "_" . $d[$this->_indexinmodel];
                    }
                    else {
                        $tdid = $this->_id . "_" . $key . "_" . $index;
                    }
                    
                    $tdclass = "tabular";
                    $td = new Td(array(
                      "id" => $tdid,
                      "class" => $tdclass
                    ));
                    $td->setDatum($op['button']);
                    $html .= $td->make("");
                    
                }
                $html .= "</tr>";
            }
            $html .= "</table>";
            return $html;
        }

        public function describe($details = "") {
            ob_start();
            echo "<br>dettagli<br>Colonne:<pre>";
            print_r($this->_columns);
            echo "</pre>";
            $contents = ob_get_contents();
            ob_end_clean();
            parent::describe($contents);
        }

        public function getColsFromModel($which_cols = "") {
            $commands = ["is", "fixedpicklist", "link"];
            $this->_columns = array();
            $model = $this->_model;
            $cols = $which_cols;
            $fields = $model->getFields();
            $fields_name = array_keys($fields);
            $fieldsalias = $model->getFieldsalias();
            foreach ($cols as $key => $col) {
                $col = explode(" as ", $col);
                if (count($col) < 2) {
                    echo "errore!";
                    return;
                }
                if (($col[1] !== "op") && (!(in_array($col[1], $commands)) || !(in_array($col[0], $fields_name)))) {
                    echo "errore!";
                    print_r($col);
                    return;
                }

                switch ($col[1]) {
                    case 'is':
                        $this->_columns[$col[0]] = $fieldsalias[$col[0]];
                        break;
                    case 'fixedpicklist':
                        $this->_columns[$col[0]] = $fieldsalias[$col[0]];
                        if (isset($col[2]) && (is_string($col[2]))) {
                            $this->_rendercommands[$col[0]] = array('fixedpicklist', explode(',', $col[2]));
                        }
                        break;
                    case 'link':
                        $this->_columns[$col[0]] = $fieldsalias[$col[0]];
                        if (isset($col[2])) {
                            $coms = explode(" WITH ", $col[2]);
                            switch (count($coms)) {
                                case 0:
                                    break;
                                case 1:
                                    $this->_rendercommands[$col[0]] = array('fixedlink', $col[2]);
                                    break;
                                default:
//                                    array_shift($coms);
                                    $this->_rendercommands[$col[0]] = array('variablelink', $coms);
                            }
                        }
                        break;
                }
            }
        }

        public function setOperation($column, $button, $idop) {
            $this->_recoperations[$column] = array(
              "idop" => $idop,
              "button" => $button
              );
            return $this;
        }

        public function setFieldOperation($col, $flag, $options) {
            if (isset($this->_columns[$col])) {
                if (isset($options['event']) && isset($options['idop']) && isset($options['index'])) {
                    $event = $options['event'];
                    $idop = $options['idop'];
                    $index = $options['index'];
                    $this->_operations[$col] = array(
                      "op" => $flag,
                      "event" => $event,
                      "idop" => $idop,
                      "index" => $index
                    );
                    if (isset($options['idsearchop'])) {
                        $this->_operations[$col]['idsearchop'] = $options['idsearchop'];
                    }
                    if (isset($options['ispicklist'])) {
                        $this->_operations[$col]['ispicklist'] = (bool) $options['ispicklist'];
                    }
                    else
                        $this->_operations[$col]['ispicklist'] = false;
                    if (isset($options['type'])) {
                        $this->_operations[$col]['type'] = $options['type'];
                    }
                    else
                        $this->_operations[$col]['type'] = "text";
                    if (isset($options['secondaryid'])) {
                        $this->_operations[$col]['secondaryid'] = $options['secondaryid'];
                    }
                    else {
                        $this->_operations[$col]['secondaryid'] = '';
                    }
                    if (isset($options['restriction'])) {
                        $this->_operations[$col]['restriction'] = $options['restriction'];
                    }
                    else {
                        $this->_operations[$col]['restriction'] = '';
                    }
                }
            }
            return $this;
        }

        public function getDataFromModel() {
            $this->_data = $this->_model->Data();
        }

    }

}