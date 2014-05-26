<?php

class Col {
    
}


function compare($a, $b) {
    if (!(is_array($a)) || !(is_array($b)))
        return 0;
    if (count($a) <= 1 || count($b) <= 1)
        return 0;
    if ($a[0] < $b[0])
        return -1;
    else if ($a[0] == $b[0])
        return 0;
    else
        return 1;
}
// nome campo, alias, tabella, aliastabella +
//  0 => normal:: niente
//  1 => fixed picklist:: valori per 0,1,2,3...
//  2 => relate(d record field:: id2, tabella1, id1 // nota: tabella1 deve essere già stata inserita
//  3 => fixed picklist + related record field:: id2, tabella1, id1, valori per 0,1,2,3... // nota: tabella1 deve essere già stata inserita
//  4 => picklist:: id2, tabella1, id1 // nota: tabella1 deve essere già stata inserita
// 10 => formula:: [nome campo = formula]


/*
 *
 * CONCAT(tabella.campo1, tabella.campo2)
 *
 */
$fields = array();
$fields[] = [0, "id", "cid", "external_collections", "a"];
$fields[] = [0, "amount", "Importo", "external_collections", "a"];
$fields[] = [1, "type", "Tipo", "external_collections", "a", "0,ASS,BON,CON"];
$fields[] = [2, "accountname", "Cliente", "vtiger_account", "b", "accountid", "external_collections", "accountid"];
$fields[] = [4, "statename", "Stato", "external_collections_state", "c", "state", "external_collections", "state"];
$fields[] = [0, "ref", "Riferimento", "external_collections", "a"];
$fields[] = [2, "bankname", "Banca", "external_banks", "d", "bankid", "external_collections", "bankid"];
$fields[] = [0, "emissiondate", "Data emissione", "external_collections", "a"];
$fields[] = [0, "receiptdate", "Data ricezione", "external_collections", "a"];
$fields[] = [0, "depositdate", "Data versamento", "external_collections", "a"];
$fields[] = [0, "valuedate", "Data valuta", "external_collections", "a"];
$fields[] = [4, "ourbankname", "Ns. Banca", "external_ourbank", "e", "ourbankid", "external_collections", "ourbankid"];
$fields[] = [10,"CONCAT(%s,%s,%s)","Formula","external_collections","a"];

echo "<pre>";
print_r($fields);
echo "</pre>";

//ordino per tipo
usort($fields, "compare");

echo "<pre>";
print_r($fields);
echo "</pre>";

//dò nomi alle tabelle
$tables = array();
$tables_alias = array();
foreach ($fields as $key => $field) {
    if (!isset($tables[$field[3]])) { // caso prima occorrenza della tabella
        switch ($field[0]) {
            case 0: //normal
            case '0':
            case 1: // fixed picklist
            case '1':

                $tables[$field[3]] = $field[3] . " " . $field[4];
                $tables_alias[$field[3]] = $field[4];
                break;
            case 2: //related record field
            case '2':
            case 3: //related record field + fixed picklist
            case '3':
            case 4: //picklist
            case '4':

                $tables[$field[3]] = $field[3] . " " . $field[4] . " ON " . $field[4] . "." . $field[5] . " = " . $tables_alias[$field[6]].".".$field[7];
                $tables_alias[$field[3]] = $field[4];
        }
        
    }
    $_fields[] = $field[4].".".$field[1]." AS '".$field[2]."'";
}
echo "<pre>";
print_r($tables);
echo "</pre>";
echo "<pre>";
print_r($tables_alias);
echo "</pre>";
echo "<pre>";
print_r($_fields);
echo "</pre>";
$SQL = "SELECT ";
$SQL .=join(", ", $_fields);
echo $SQL . "<br><br>";
$SQL .= " FROM ";
$SQL .=join(" JOIN ", $tables);
echo $SQL . "<br><br>";
?>
