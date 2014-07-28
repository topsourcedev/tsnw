<?php

/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */
include 'config.inc.php';

$link = mysqli_connect($host, $user, $password, $db) OR die("impossibile connettersi");
$sql = "";