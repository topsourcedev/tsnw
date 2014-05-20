<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 * 
 * 
 * 
 * Questo è il file di configurazione iniziale, utile per l'installazione dell'applicazione e per i successivi
 * interventi di manutenzione della struttura.
 * 
 */

/*
 *  NON RIMUOVERE O ALTERARE LE SEGUENTI RIGHE
 */
$database = array();
$language = array();
$module = array();


/* * *******************
 *                     *
 *     DATABASES       *
 *                     *
 * * ***************** */

// per aggiungere un db aggiungere una riga come la seguente:
//$database [] = ["name","config_file","format", "flag"];
//name è il nome interno del db 
//config file è il nome senza estensione del file di configurazione del db
//format è l'estensione, al momento sono supportati i formati: ini
//flag è uno dei seguenti valori: initial|mandatory|onrequest, in particolare
//initial: indica che il db viene caricato all'avvio dell'applicazione (dunque è richiesto)
//mandatory: indica che il db è indispensabile all'applicazione ma in fase di avvio viene solo controllata l'esistenza della configurazione
//onrequest: indica che il db viene caricato su particolare richiesta a runtime e non è indispensabile


$database [] = ["vtiger","vtiger","ini", "initial"];
$database [] = ["local","localdb","ini", "initial"];
$database [] = ["vtigertest","vtigertest","ini", "onrequest"];






$language [] = ["english","en_US"];
$language [] = ["italiano","it_IT"];



$module [] = ["incassi","incassi"];