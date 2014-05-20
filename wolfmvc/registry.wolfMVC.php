<?php

namespace WolfMVC
{
    /**
     * Registro delle classi istanziate
     */
    class Registry
    {
        /**
         *
         * @var array Questa è la memoria del registro. 
         */
        private static $_instances = array();
        
        private function __construct()
        {
            // do nothing
        }
        
        private function __clone()
        {
            // do nothing
        }
        
        /**
         * Getter per valori nel registro
         * @param mixed $key
         * @param mixed $default
         * @return mixed
         */
        public static function get($key, $default = null)
        {
            if (isset(self::$_instances[$key]))
            {
                return self::$_instances[$key];
            }
            return $default;
        }
        
        /**
         * Setter per valori nel registro
         * @param mixed $key
         * @param mixed $instance
         */
        
        public static function set($key, $instance = null)
        {
            self::$_instances[$key] = $instance;
        }
        
        /**
         * Cancella valori memorizzati nel registro (se esistono)
         * @param mixed $key
         */
        public static function erase($key)
        {
            unset(self::$_instances[$key]);
        }
        
        public static function esponi(){
            echo "<br><br><h1>BEGIN REGISTRY DUMP</h1><BR>";
            echo "<pre>";
            foreach (self::$_instances as $key => $instance){
                echo "<strong>".$key."</strong>\n";
                print_r($instance);
            }
            echo "</pre>";
        }
    }
}