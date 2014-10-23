<?php

class Tsnwoperation extends WolfMVC\Base {

    public $version = "0.1";

    /**
     * @readwrite
     * @var stdClass
     */
    protected $_analyzedrequest;
    private $_model;

    public function __construct($options = array()) {
        $this->_model = new Tsnwmodel();
        parent::__construct($options);
    }

    protected function anti_injection($input) {
        $pulito = strip_tags(addslashes(trim($input)));
        $pulito = str_replace("'", "\'", $pulito);
        $pulito = str_replace('"', '\"', $pulito);
        $pulito = str_replace(';', '\;', $pulito);
        $pulito = str_replace('--', '\--', $pulito);
        $pulito = str_replace('+', '\+', $pulito);
        $pulito = str_replace('(', '\(', $pulito);
        $pulito = str_replace(')', '\)', $pulito);
        $pulito = str_replace('=', '\=', $pulito);
        $pulito = str_replace('>', '\>', $pulito);
        $pulito = str_replace('<', '\<', $pulito);
        return $pulito;
    }

    public function executeGet() {

        $vm = $this->_model->extractByPath($this->_analyzedrequest->mainres);
        $result = "";
        //webservice o sql?
        ////webservice
        if (isset($vm["vtiger_module"]))
        {
            $client = \WolfMVC\Registry::get("VTWStest");
            $vm = $vm["vtiger_module"];
            if (!isset($vm["retrieveable"]) || $vm["retrieveable"] !== TRUE)
            {
                throw new Tsnwexception("Can't retrieve this resource", 403, null);
            }
            //retrieve o query?
            //retrieve
            if (isset($this->_analyzedrequest->mainresid) && !empty($this->_analyzedrequest->mainresid))
            {
//                $result = "Faccio un retrieve con i WS.";
//                $result .= " Cerco ".$vm["idPrefix"]."x".$this->_analyzedrequest->mainresid;
                foreach ($this->_analyzedrequest->path as $l => $arr) {
                    $idPrefix = "";
                    foreach ($arr as $k => $v) {
                        if ($k === "ref")
                        {
                            $tmpvm = $this->_model->extractByPath($v);
                            if ($tmpvm && isset($tmpvm["vtiger_module"]) && isset($tmpvm["vtiger_module"]["idPrefix"]))
                            {
                                $idPrefix = $tmpvm["vtiger_module"]["idPrefix"] . "x";
                            }
                        }
                    }
                    foreach ($arr as $k => $v) {
                        if ($k !== "ref")
                        {
                            $WHERE [] = array($this->anti_injection($k) => $idPrefix . $this->anti_injection($v));
                        }
                    }
                }
                $result = $client->doRetrieve($vm["idPrefix"] . "x" . $this->anti_injection($this->_analyzedrequest->mainresid));
                if (is_string($result) && strtolower($result) !== "false")
                {
                    foreach ($WHERE as $l => $arr) {
                        foreach ($arr as $k => $v) {
                            if (isset($result[$k]) && $result[$k] !== $v)
                            {
                                throw new Tsnwexception("No match", 404, null);
                            }
                        }
                    }
                }
            }

            //query
            else
            {
                $result = "Faccio una query con i WS.";
//                $result = print_r($this->_analyzedrequest->path,true);
                $WHERE = array();
                foreach ($this->_analyzedrequest->path as $l => $arr) {
                    $idPrefix = "";
                    foreach ($arr as $k => $v) {
                        if ($k === "ref")
                        {
                            $tmpvm = $this->_model->extractByPath($v);
                            if ($tmpvm && isset($tmpvm["vtiger_module"]) && isset($tmpvm["vtiger_module"]["idPrefix"]))
                            {
                                $idPrefix = $tmpvm["vtiger_module"]["idPrefix"] . "x";
                            }
                        }
                    }
                    foreach ($arr as $k => $v) {
                        if ($k !== "ref")
                        {
                            $WHERE [] = $this->anti_injection($k) . " = '" . $idPrefix . $this->anti_injection($v) . "'";
                        }
                    }
                }
                foreach ($this->_analyzedrequest->mainresquerypars as $k => $v) {
                    $WHERE [] = $this->anti_injection($k) . " = '" . $this->anti_injection($v) . "'";
                }
                $WHERE = join(" AND ", $WHERE);
                if ($WHERE !== "")
                    $WHERE = " WHERE " . $WHERE;
//                $result = "SELECT * FROM " . ucfirst($vm["name"]) . $WHERE;
                $result = $client->doQuery("SELECT * FROM " . ucfirst($vm["name"]) . $WHERE);
                if (is_string($result) && strtolower($result + "") === "false")
                {
                    throw new Tsnwexception("No match", 404, null);
                }
            }
        }


        //sql
        else if (isset($vm["custom_description"]))
        {
            $vm = $vm["custom_description"];
            if (!isset($vm["retrieveable"]) || $vm["retrieveable"] !== TRUE)
            {
                throw new Tsnwexception("Can't retrieve this resource", 403, null);
            }

            $sql = "SELECT * FROM " . $vm["tablesforquery"];
            $WHERE = array();
            if ($this->_analyzedrequest->mainresid !== "")
            {
                $WHERE[] = $vm["idField"] . " = '" . $this->_analyzedrequest->mainresid . "'";
            }
            else
            {
                foreach ($this->_analyzedrequest->mainresquerypars as $k => $v) {
                    $WHERE [] = $this->anti_injection($k) . " = '" . $this->anti_injection($v) . "'";
                }
            }
            foreach ($this->_analyzedrequest->path as $l => $arr) {
                foreach ($arr as $k => $v) {
                    if ($k !== "ref")
                    {
                        $WHERE [] = $this->anti_injection($k) . " = '" . $this->anti_injection($v) . "'";
                    }
                }
            }
            $WHERE = join(" AND ", $WHERE);
            if ($WHERE !== "")
            {
                $WHERE = " WHERE " . $WHERE;
            }
            $sql .= $WHERE;
            $database = $vm["database"];
            $db = \WolfMVC\Registry::get("database_" . $database);
            $link = new mysqli($db->getHost(), $db->getUsername(), $db->getPassword(), $db->getSchema());
            if ($link->connect_errno)
            {
                throw new Tsnwexception("DB connection error", 500, null);
            }
            $queryresult = $link->query($sql);
            if (!$queryresult)
            {
                throw new Tsnwexception("DB query error", 500, null);
            }
            $result = $queryresult->fetch_all(MYSQLI_ASSOC);
            if (count($result) === 0 || $queryresult->num_rows === 0)
            {
                throw new Tsnwexception("No match", 404, null);
            }
        }
        return $result;
    }

}
?>

