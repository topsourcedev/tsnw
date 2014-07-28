<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {

    /**
     * Semantic Multi Model
     */
    class Smm extends Base {

        /**
         * @readwrite
         */
        protected $_mmodname;

        /**
         * @readwrite
         */
        protected $_mname;

        /**
         * @readwrite
         */
        protected $_DBTables = array();

        /**
         * @readwrite
         */
        protected $_MODTables = array();

        /**
         * @readwrite
         */
        protected $_MODElements = array();

        /**
         * @readwrite
         */
        protected $_MODRelations = array();

        /**
         * @readwrite
         */
        protected $_maintable = array();

        /**
         * @readwrite
         */
        protected $_model = array();
        protected $_tmpmodel = array();
        protected $_selectStructure;
        protected $_hash = array();

        /**
         *
         * @readwrite
         */
        protected $_savedQuery = array();

        
        protected $_chunks = array();



        private function hashing($len) {
            $letters = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

            while (true) {
                $string = "";
                for ($i = 0; $i < $len; $i++) {
                    $string .= $letters[rand(0, 25)];
                }
                if (!isset($this->_hash->{$string})) {
                    $this->_hash->{$string} = $string;
                    return $string;
                }
            }
        }

        private function defineSubQuery($tableData, $tabs, $fies) {
            $queryalias = $this->hashing(8);
//            echo "<br>subquery<br>";
//            print_r($fies);
            $queryfields = array();
            $newfields = array();
            foreach ($tableData->fields as $fieldkey => $field) {
                //la sottoquery estrae i campi indicati
                $tableData->fields[$fieldkey]->tmpfieldalias = $this->hashing(4);

                array_push($queryfields, "GROUP_CONCAT(" . $field->belongingtablealias . "." . $field->fieldname . ") AS " . $tableData->fields[$fieldkey]->tmpfieldalias);

                $tableData->fields[$fieldkey]->belongingtablealias = $queryalias;
                array_push($newfields, $tableData->fields[$fieldkey]);
            }
            //la sottoquery estrae anche il campo per la clausola on, indipendentemente dal fatto
            //che questo campo sia già stato richiesto per l'output finale
            $onClauseAlias = $this->hashing(4);
            array_push($queryfields, "GROUP_CONCAT(" . $tableData->MODTableId . "." . $tableData->linkDBFieldName . ") AS " . $onClauseAlias);
            $query = "(SELECT " . join(", ", $queryfields) . " FROM " . $tableData->tablenameindb . " " . $tableData->MODTableId . " GROUP BY " . $tableData->linkDBFieldName . ")";
            //la tabella che porto sopra è la query
            $tableData->tablenameindb = $query;
            $MODTableId = $tableData->MODTableId;
            $tableData->MODTableId = $queryalias;
            $tableData->linkDBFieldName = $onClauseAlias;
            $newtab = $this->defineSubTable($tableData, $tabs);
            $tableData->MODTableId = $MODTableId;
            return array($newtab, $newfields);
        }

        private function defineSubTable($tableData) {
            $newtab = new \stdClass();
            $newtab->tablename = $tableData->tablenameindb;
            $newtab->tablealias = $tableData->MODTableId;
            $newtab->linkDBFieldName = $tableData->linkDBFieldName;
            $newtab->parentLinkDBFieldName = $tableData->parentLinkDBFieldName;
            $newtab->linkDBFieldName = $tableData->linkDBFieldName;
            $newtab->parentLinkDBFieldName = $tableData->parentLinkDBFieldName;
            return $newtab;
        }

        private function identifyTable($path) {
            $table = $path[count($path) - 1];
            $MODTable = $this->_MODTables[$table];
            $tablenameindb = explode(".", $MODTable->{"DBTable"});
            $tablenameindb = $tablenameindb[1];
            $childrenNumber = count($MODTable->{"MODRelations"});
            $tableData = new \stdClass();
            $tableData->tablenameindb = $tablenameindb;
            $tableData->MODTableId = $MODTable->MODTableId;
            $tableData->parentMODTableId = $MODTable->parentMODTableId;
            $tableData->linkDBFieldName = $MODTable->linkDBFieldName;
            $tableData->parentLinkDBFieldName = $MODTable->parentLinkDBFieldName;
//            $tableData->field = new \stdClass();
//            $tableData->field->byFieldId = array();
//            $tableData->field->byFieldName = array();
//            foreach ($MODTable->MODFields as $key => $field){
//                $tableData->field->byFieldId[$field->MODFieldId] = $field->DBFieldName;
//                $tableData->field->byFieldName[$field->DBFieldName] = $field->MODFieldId;
//            }
            $tableData->fields = array();
            $tableData->MODElements = $MODTable->MODElements;
            foreach ($MODTable->MODElements as $MODElementId) {
                $MODElement = $this->_MODElements[$MODElementId];
                $MODFields = $MODTable->MODFields;
                foreach ($MODFields as $MODFkey => $MODF) {
                    if ($MODF->MODFieldId === $MODElement->MODFieldId) {
                        $MODField = $MODF;
                        break;
                    }
                }
                $field = new \stdClass();
                $field->fieldname = $MODField->DBFieldName; //al livello superiore si vede questo campo
                $field->deffieldalias = $MODElement->MODElementName; // che si chiamerà così
                $field->tmpfieldalias = ""; //che per ora si chiama così
                $field->belongingtablealias = $MODTable->MODTableId; //che appartiene alla tabella che per ora si chiama così
                $filter = (array) $MODElement->MODElementFilter;
                if (!(empty($filter)))
                    $field->filter = $MODElement->MODElementFilter;
                array_push($tableData->fields, $field);
            }

            $tableData->MODRelations = $MODTable->MODRelations;
            $tableData->childrenNumber = $childrenNumber;
            return $tableData;
        }

        private function solvefilter($field) {
            $OUTFILTER = "TTTTTT";
            $FIELD = ($field->tmpfieldalias === "" ? $field->fieldname : $field->tmpfieldalias);
            $TABLE = $field->belongingtablealias;
            $groups = $field->filter;
            foreach ($groups as $groupk => $group) { //group è un oggetto {filter:array,con:string}
                switch (count($group->filters)) {
                    case 0:
                        continue;
                        break;
                    case 1:
                        $GROUP = "SSSSSS";
                        break;
                    default :
                        $GROUP = "(SSSSSS)";
                        break;
                }

                foreach ($group->filters as $filterk => $filter) { //filter è un oggetto {first:string,op:string,second:string,con:string}
                    $filter->first = str_replace("{FIELD}", $TABLE . "." . $FIELD, $filter->first);
                    $filter->second = str_replace("{FIELD}", $TABLE . "." . $FIELD, $filter->second);
                    if (isset($group->filters[$filterk + 1])) {
                        $GROUP = str_replace("SSSSSS", $filter->first . " " . $filter->op . " " . $filter->second . " " . $filter->con . " SSSSSS", $GROUP);
                    } else {
                        $GROUP = str_replace("SSSSSS", $filter->first . " " . $filter->op . " " . $filter->second, $GROUP);
                    }
                }
                if (isset($groups[$groupk + 1])) {
                    $OUTFILTER = str_replace("TTTTTT", $GROUP . " " . $group->con . " TTTTTT", $OUTFILTER);
                } else {
                    $OUTFILTER = str_replace("TTTTTT", $GROUP, $OUTFILTER);
                }
            }
            $field->filter = $OUTFILTER;
            return $OUTFILTER;
        }

        private function solvetable($path, $mult) {
            //$path è un vettore
            $tablenode = $this->_selectStructure->tables;
            for ($key = 0; $key < count($path) - 1; $key++) {
                $tablenode = $tablenode->{$path[$key]};
            }
            $tableData = $this->identifyTable($path, $mult);
//            echo "INIZIO TABELLA " . $tableData->tablenameindb . " (" . $tableData->MODTableId . ")";
//            echo "<br>Dati tabella :<pre>";
//            print_r($tableData);
//            echo "</pre>";
            if ($tableData->childrenNumber === 0) { // caso foglia
//                echo "<br>>>>>>FOGLIA<br>";
//                echo "I campi sono ";
//                print_r($tableData->fields);
                if ($tablenode->{$tableData->MODTableId} === "")
                    $tablenode->{$tableData->MODTableId} = new \stdClass();
                switch ($mult) {
                    case '!':
                        //i campi che porto sopra sono gli stessi che vedo qui
                        $tablenode->{$tableData->MODTableId}->fies = $tableData->fields;
                        //la tabella che porto sopra è la tabella stessa
                        $tablenode->{$tableData->MODTableId}->tabs = array();
                        $newtab = $this->defineSubTable($tableData);
//                        echo "<br>newtab ";
//                        print_r($newtab);
                        $tablenode->{$tableData->MODTableId}->tabs[0] = $newtab;
                        break;
                    case '*':
                        //i campi che porto sopra sono gli stessi che vedo qui
                        $tablenode->{$tableData->MODTableId}->fies = $tableData->fields;
                        //la tabella che porto sopra è la tabella stessa
                        $tablenode->{$tableData->MODTableId}->tabs = array();
                        $newtab = $this->defineSubTable($tableData);
//                        echo "<br>:::newtab:::  ";
//                        print_r($newtab);
                        $tablenode->{$tableData->MODTableId}->tabs[0] = $newtab;
                        break;
                    case '+':
                        //devo produrre una subquery
                        $newquery = $this->defineSubQuery($tableData, array(), array());
//                        echo "<br>:::newquery:::  ";
//                        print_r($newquery);
                        $tablenode->{$tableData->MODTableId}->fies = $newquery[1];
                        //la tabella che porto sopra è la query
                        $tablenode->{$tableData->MODTableId}->tabs = array();
                        $tablenode->{$tableData->MODTableId}->tabs[0] = $newquery[0];
                        break;
                }
//                echo "<pre>";
//                print_r($tablenode);
//                echo "</pre>";
                return;
            } else {
//                echo "<br>>>>>>RAMO<br>";
                if ($tablenode->{$tableData->MODTableId} === "")
                    $tablenode->{$tableData->MODTableId} = new \stdClass();

                $tablenode->{$tableData->MODTableId}->fies = array();
                $tablenode->{$tableData->MODTableId}->tabs = array();
                foreach ($tableData->MODRelations as $MODRelation) {
                    $childtable = $this->_MODRelations[$MODRelation]->{"MODTableToId"};
//                    echo "<br>passo al figlio : " . $childtable . "<br>";
                    $newpath = array_merge($path, array($childtable));
                    if ($tablenode->{$tableData->MODTableId} === "")
                        $tablenode->{$tableData->MODTableId} = new \stdClass();
                    $tablenode->{$tableData->MODTableId}->{$childtable} = "";

                    $this->solvetable($newpath, $this->_MODRelations[$MODRelation]->{"MODRelationMult"});
//                    echo "fine ricorsione";
                    $childfies = $tablenode->{$tableData->MODTableId}->{$childtable}->fies;
                    $childtabs = $tablenode->{$tableData->MODTableId}->{$childtable}->tabs;
//                    echo "<br><br><br>torno dalla ricorsione a " . $tableData->tablenameindb . "<br>";
//                    echo "riporto su i campi dei livelli inferiori e li ammazzo nei livelli inferiori";
                    $tablenode->{$tableData->MODTableId}->fies = array_merge($tablenode->{$tableData->MODTableId}->fies, $childfies);
                    unset($tablenode->{$tableData->MODTableId}->{$childtable}->fies);
                    //prima di copiare le tabelle applico la on clause e la giusta join
//                    echo "<br> Relation:::<br>";
                    $pm = $this->_MODRelations[$MODRelation]->MODRelationPM;
                    $gv = $this->_MODRelations[$MODRelation]->MODRelationGenericValue;
                    foreach ($childtabs as $childtabkey => $t) {
                        if (isset($t->linkDBFieldName) && isset($t->parentLinkDBFieldName)) {
                            $t->onClause = "ON (" . $t->tablealias . "." . $t->linkDBFieldName . " = " . $tableData->MODTableId . "." . $t->parentLinkDBFieldName . ")";
                            unset($t->linkDBFieldName);
                            unset($t->parentLinkDBFieldName);
                        }
                        if (!isset($t->join)) {
                            if ($pm) {
                                $t->join = "LEFT JOIN";
                            } else {
                                $t->join = "JOIN";
                            }
                        }
                        array_push($tablenode->{$tableData->MODTableId}->tabs, $t);
                    }

                    unset($tablenode->{$tableData->MODTableId}->{$childtable}->tabs);
                    $tablenode->{$tableData->MODTableId}->{$childtable} = "Tabella analizzata";
//                    echo "<pre>";
//                    print_r($tablenode);
//                    echo "</pre>";
                }
//                echo "ora mi occupo della tabella in corso " . $tableData->tablenameindb;
                $MODElements = $tableData->{"MODElements"};


                switch ($mult) {
                    case '!':
//                        echo "vengo da una relazione 1:1<br>";
                        //i campi che porto sopra sono gli stessi che vedo qui
                        $tablenode->{$tableData->MODTableId}->fies = array_merge($tablenode->{$tableData->MODTableId}->fies, $tableData->fields);
                        //la tabella che porto sopra è la tabella stessa
                        $newtab = $this->defineSubTable($tableData, $tablenode->{$tableData->MODTableId}->tabs);
//                        echo "<br>:::newtab:::  ";
//                        print_r($newtab);
//                        $newtab = new \stdClass();
//                        $newtab->tablename = $tablenameindb;
//                        $newtab->tablealias = $MODTable->MODTableId;
//                        $newtab->linkDBFieldName = $MODTable->linkDBFieldName;
//                        $newtab->parentLinkDBFieldName = $MODTable->parentLinkDBFieldName;
                        array_unshift($tablenode->{$tableData->MODTableId}->tabs, $newtab);
                        break;
                    case '*':
//                        echo "vengo da una relazione n:1<br>";
                        //i campi che porto sopra sono gli stessi che vedo qui
                        $tablenode->{$tableData->MODTableId}->fies = array_merge($tablenode->{$tableData->MODTableId}->fies, $tableData->fields);
                        //la tabella che porto sopra è la tabella stessa
                        $newtab = $this->defineSubTable($tableData, $tablenode->{$tableData->MODTableId}->tabs);
//                        echo "<br>:::newtab:::  ";
//                        print_r($newtab);
//                        $newtab = new \stdClass();
//                        $newtab->tablename = $tablenameindb;
//                        $newtab->tablealias = $MODTable->MODTableId;
//                        $newtab->linkDBFieldName = $MODTable->linkDBFieldName;
//                        $newtab->parentLinkDBFieldName = $MODTable->parentLinkDBFieldName;
                        array_unshift($tablenode->{$tableData->MODTableId}->tabs, $newtab);
                        break;
                    case '+':
//                        echo "vengo da una relazione 1:n<br>";
                        //devo produrre una subquery
                        $queryalias = $this->hashing(8);

                        $queryfields = array();

                        foreach ($tablenode->{$tableData->MODTableId}->fies as $prevfieldkey => $prevfield) { // metto i campi delle sottotabelle nella sottoquery e tra i campi di questa tabella
                            $newfieldalias = $this->hashing(4);
                            $nome = ($prevfield->tmpfieldalias === "" ? $prevfield->fieldname : $prevfield->tmpfieldalias);
                            array_push($queryfields, "GROUP_CONCAT(" . $prevfield->belongingtablealias . "." . $nome . ") AS " . $newfieldalias);
                            $tablenode->{$tableData->MODTableId}->fies[$prevfieldkey]->tmpfieldalias = $newfieldalias;
                            $tablenode->{$tableData->MODTableId}->fies[$prevfieldkey]->belongingtablealias = $queryalias;
                        }
                        $whereclause = "";
                        foreach ($fields as $fieldkey => $field) { // metto i campi nuovi nella sottoquery e in questa tabella-struttura
                            $fields[$fieldkey]->tmpfieldalias = $this->hashing(4);
                            $fields[$fieldkey]->belongingtablealias = $queryalias;
                            array_push($queryfields, "GROUP_CONCAT(" . $tableData->MODTableId . "." . $field->fieldname . ") AS " . $fields[$fieldkey]->tmpfieldalias);
                            array_push($tablenode->{$tableData->MODTableId}->fies, $fields[$fieldkey]);
                        }
                        $onClauseAlias = $this->hashing(4);
                        array_push($queryfields, "GROUP_CONCAT(" . $tableData->MODTableId . "." . $tableData->linkDBFieldName . ") AS " . $onClauseAlias);
                        //le tabelle interne alla subquery stavolta possono essere più di una e vanno joinate
                        $tablesforquery = array();
                        foreach ($tablenode->{$table}->tabs as $tablekey => $tab) {
                            array_push($tablesforquery, $tab->tablename . " " . $tab->tablealias);
                        }
                        $query = "(SELECT " . join(", ", $queryfields) . " FROM " . $tablenameindb . ")";
                        $tablenode->{$table}->fies = $fields;
                        //la tabella che porto sopra è la query
                        $tablenode->{$table}->tabs = array(); //questo resetta anche le tabelle proveniente da passi inferiori
                        $tablenode->{$table}->tabs[0] = new \stdClass();
                        $tablenode->{$table}->tabs[0]->tablename = $query;
                        $tablenode->{$table}->tabs[0]->tablealias = $queryalias;
                        $tablenode->{$table}->tabs[0]->linkDBFieldName = $onClauseAlias;
                        $tablenode->{$table}->tabs[0]->parentLinkDBFieldName = $MODTable->parentLinkDBFieldName;
                        break;
                }
            }

//            echo "<pre>";
//            print_r($tablenode);
//            echo "</pre>";
            if ($tableData->MODTableId === $this->_maintable) { // conclusione
                $concludingfields = array();
                $concludingtables = array($tableData->tablenameindb . " " . $tableData->MODTableId);
                $concludingfilter = "";
                $filterflag = false;
                foreach ($tableData->fields as $key => $f) {
                    if (isset($f->filter)) {
                        $filter = $this->solvefilter($f);
                        if ($filterflag) {
                            $concludingfilter .= " AND " . $filter;
                        } else {
                            $concludingfilter .= " WHERE " . $filter;
                        }
                    }

                    array_push($concludingfields, $f->belongingtablealias . "." . $f->fieldname . " AS '" . $f->deffieldalias . "'");
                }
                foreach ($tablenode->{$tableData->MODTableId}->fies as $key => $f) {
                    if (isset($f->filter)) {
                        $filter = $this->solvefilter($f);
                        if ($filterflag) {
                            $concludingfilter .= " AND " . $filter;
                        } else {
                            $concludingfilter .= " WHERE " . $filter;
                        }
                    }
                    $fieldname = ($f->tmpfieldalias === "" ? $f->fieldname : $f->tmpfieldalias);
                    array_push($concludingfields, $f->belongingtablealias . "." . $fieldname . " AS '" . $f->deffieldalias . "'");
                }
//                echo "<br><br>";

                $concludingfields = join(", ", $concludingfields);
//                print_r($concludingfields);
                foreach ($tablenode->{$tableData->MODTableId}->tabs as $key => $t) {
                    if (isset($t->linkDBFieldName) && isset($t->parentLinkDBFieldName)) {
                        $t->onClause = "ON (" . $t->tablealias . "." . $t->linkDBFieldName . " = " . $tableData->MODTableId . "." . $t->parentLinkDBFieldName . ")";
                        unset($t->linkDBFieldName);
                        unset($t->parentLinkDBFieldName);
                    }
                    array_push($concludingtables, $t->join . " " . $t->tablename . " " . $t->tablealias . " " . $t->onClause);
                }
//                echo "<br><br>";
//                echo "<pre>";
//                print_r($tablenode);
//                echo "</pre>";
                $concludingtables = join(" ", $concludingtables);
//                print_r($concludingtables);
                $sql = "SELECT " . $concludingfields . " FROM " . $concludingtables . " " . trim($concludingfilter);

                return $sql;
            }
//            echo "<br>che campi ci sono?<br>";
//            echo "path attuale : ";
//            
        }

        public function makeSelectAll() {

            $this->structure(false);
            $this->_tmpmodel = $this->_model;
            $this->_selectStructure = new \stdClass();
            $this->_selectStructure->tables = new \stdClass();
            $this->_selectStructure->tables->{$this->_maintable} = "";
            $this->_selectStructure->fields = "";
            $this->_savedQuery["selectAll"] = $this->solvetable(array($this->_maintable), "");
        }

        public function getAllData() {
            $this->makeSelectAll();
            $sql = $this->savedQuery["selectAll"];
//            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
            $link = new \mysqli("localhost", "root", "root", "vtiger540");
            $result = $link->query($sql);
            $fields = array();
            $fieldsext = mysqli_fetch_fields($result);
            foreach ($fieldsext as $key => $field) {
                array_push($fields, $field->name);
            }
            return array("data" => mysqli_fetch_all($result, MYSQLI_ASSOC), "fields" => $fields);
        }

        public function structure($verbose) {
            $ini = array();
            $link = new \mysqli("localhost", "root", "root", "vtiger540");
//            $link = new \mysqli("vtiger-rec.cjybxwcd2ntt.us-west-2.rds.amazonaws.com", "vtiger", "144Lisboa", "vtiger540");
            $sql = "SELECT `macromodel`,`model`,`key`,`value` FROM external_tsnw_model WHERE macromodel = '" . $this->mmodname . "'";
            $result = $link->query($sql);
            while ($riga = mysqli_fetch_assoc($result)) {
                $ini[$riga["macromodel"] . "." . $riga["model"] . "." . $riga["key"]] = $riga["value"];
            }
            if ($verbose) {
                echo "modello " . $this->_mmodname . "." . $this->_mname;
                echo "<br>";
            }
            $data = $this->initoarray($ini, $this->_mmodname . "." . $this->_mname);

            $DBTables = json_decode($data["DBTables"]);

            foreach ($DBTables as $DBTablekey => $DBTable) {
                $this->_DBTables[$DBTable] = json_decode($data["DBTable_" . $DBTable]);
                $MODTables = $this->_DBTables[$DBTable]->{"MODTables"};
                foreach ($MODTables as $MODTablekey => $MODTable) {
                    $this->_MODTables[$MODTable] = json_decode($data["MODTable_" . $MODTable]);
                    $MODElements = $this->_MODTables[$MODTable]->{"MODElements"};
                    foreach ($MODElements as $MODElementkey => $MODElement) {
                        $this->_MODElements[$MODElement] = json_decode($data["MODElement_" . $MODElement]);
                    }
                    $MODRelations = $this->_MODTables[$MODTable]->{"MODRelations"};
                    foreach ($MODRelations as $MODRelationkey => $MODRelation) {
                        $this->_MODRelations[$MODRelation] = json_decode($data["MODRelation_" . $MODRelation]);
                    }
                }
            }

            $this->_maintable = $data["maintable"];
            $this->_model = json_decode($data["model"]);
            $structure = $this->_model->{"structure"};
            if ($this->_model->{"maintable"} === "") {
                $this->_model->{"maintable"} = "table_" . $this->_maintable;
            }
            $this->_hash = json_decode($data["hash"]);

            if ($verbose) {
                echo "<br><br><br>Riassunto dati:<br>";
                echo "maintable " . $this->maintable;
                echo "<br>Model<pre>";
                print_r($this->_model);
                echo "</pre>";
                echo "<br>Elenco dbtables gonfiate<pre>";
                print_r($this->_DBTables);
                echo "</pre>";
                echo "Elenco modtables gonfiate<pre>";
                print_r($this->_MODTables);
                echo "</pre>";
                echo "Elenco modelements gonfiati<pre>";
                print_r($this->_MODElements);
                echo "</pre>";
                echo "Elenco modrelations gonfiate<pre>";
                print_r($this->_MODRelations);
                echo "</pre>";
                echo "<pre>";
                print_r($data);
                echo "</pre>";
            }
        }

        public function initoarray($ini, $prefix) {
            $output = array();
            foreach ($ini as $key => $value) {
                if ($key === $prefix) { //(#)
                    $output[0] = $value;
                } else {
                    if (strpos($key, $prefix . ".") === 0) {
                        $kk = str_ireplace($prefix . ".", "", $key);
                        $out = $value;
                        $output[$kk] = $value;
                    }
                }
            }

            return $output;
        }

    }

}
