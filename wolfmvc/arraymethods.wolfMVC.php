<?php

namespace WolfMVC {

    /**
     * Fornisce metodi derivati per trattare array
     */
    class ArrayMethods {

        private function __construct() {
            // do nothing
        }

        private function __clone() {
            // do nothing
        }

        /**
         * restituisce l'array di input privato degli elementi considerati empty.
         * @param array $array
         * @return array
         */
        public static function clean($array) {
            return array_filter($array, function($item) {
                return !empty($item);
            });
        }

        /**
         * Esegue trim su tutti gli elementi dell'array.
         * @param array $array
         * @return array
         */
        public static function trim($array) {
            return array_map(function($item) {
                return trim($item);
            }, $array);
        }

        /**
         * Restituisce il primo elemento dell'array passato, ovvero l'elemento con la chiave di indice 0.
         * @param array $array
         * @return mixed
         */
        public static function first($array) {
            if (sizeof($array) == 0) {
                return null;
            }

            $keys = array_keys($array);
            return $array[$keys[0]];
        }

        /**
         * Restituisce l'ultimo elemento dell'array passato, ovvero l'elemento con la chiave di indice length-1.
         * @param array $array
         * @return mixed
         */
        public static function last($array) {
            if (sizeof($array) == 0) {
                return null;
            }

            $keys = array_keys($array);
            return $array[$keys[sizeof($keys) - 1]];
        }

        /**
         * Trasforma un array in oggetto.
         * @param array $array
         * @return \stdClass
         */
        public static function toObject($array) {
            $result = new \stdClass();

            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $result->{$key} = self::toObject($value);
                }
                else {
                    $result->{$key} = $value;
                }
            }

            return $result;
        }

        /**
         * Appiattisce un array multidimensionale su uno monodimensionale
         * @param array $array
         * @param array $return
         * @return array
         */
        public function flatten($array, $return = array()) {
            foreach ($array as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $return = self::flatten($value, $return);
                }
                else {
                    $return[] = $value;
                }
            }

            return $return;
        }

        public function toQueryString($array) {
            return http_build_query(
              self::clean(
                $array
              )
            );
        }

        /**
         * Trasforma un array php nella stringa di definizione e dichiarazione di quell'array in Js.
         * Funziona con array monodimensionali i cui elementi sono stringhe.
         * @param array $array
         * @return string Una stringa del tipo "new Array(item1,item2,...,itemN)"
         */
        public static function toJSArray($array) {
            if (empty($array)){
                return "new Array()";
            }
            $string = "new Array(";
            foreach ($array as $item) {
                $string .= "'" . $item . "', ";
            }
            $string[strlen($string) - 2] = ")";
            return $string;
        }

    }

}