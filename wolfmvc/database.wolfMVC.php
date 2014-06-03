<?php

namespace WolfMVC
{
    use WolfMVC\Base as Base;
    use WolfMVC\Events as Events;
    use WolfMVC\Registry as Registry;
    use WolfMVC\Database as Database;
    use WolfMVC\Database\Exception as Exception;
    
    class Database extends Base
    {
        /**
        * @readwrite
        */
        protected $_type;
        
        /**
        * @readwrite
        */
        protected $_options;
        
        protected function _getExceptionForImplementation($method)
        {
            return new Exception\Implementation("{$method} method not implemented");
        }
        /**
         * 
         * @param type $conf
         * @return \WolfMVC\Database\Connector\Mysql
         * @throws Exception\Argument
         */
        public function initialize($conf)
        {
//            echo "conf = ".$conf."<br>";
            if (!$this->type)
            {
                $configuration = \WolfMVC\Registry::get("configuration");
                if ($configuration)
                {
                    $configuration = $configuration->initialize(); //restituisce il driver
                    $parsed = $configuration->parse("application/configuration/".$conf);
                    if (!empty($parsed->database->default) && !empty($parsed->database->default->type))
                    {
                        $this->type = $parsed->database->default->type;
                        unset($parsed->database->default->type);
                        $this->options = (array) $parsed->database->default;
                    }
                }
            }
            
            if (!$this->type)
            {
                throw new Exception\Argument("Invalid type");
            }
            
            switch ($this->type)
            {
                case "mysql":
                {
                    return new Database\Connector\Mysql($this->options);
                    break;
                }
                default:
                {
                    throw new Exception\Argument("Invalid type");
                    break;
                }
            }
        }
    }
}