<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC {


    class Utils {
        /**
         * Esegue stripslashes ricorsivamente sugli elementi degli array passati
         * @param array $array
         * @return mixed
         */
        public static function _strips($array) {
            if (is_array($array)) {
                return array_map(__CLASS__ . "::_strips", $array);
            }
            return stripslashes($array);
        }

        

    }

}