<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Component {

    class Simpleform extends Form {

        

        /**
         * @todo aggiungere eccezioni
         * Genera il template di un form
         */
        public function make($html) {
            $html .= "<form";
            if (isset($this->_action)) {
                $html .= " action=\"{$this->_action}\"";
            }
            if (isset($this->_method)) {
                $html .= " method=\"{$this->_method}\"";
            }
            $html .= ">";
            if (isset($this->_formlabel) && !(empty($this->_formlabel))) {
                $html .= " <fieldset>\n<legend>" . $this->_formlabel . "</legend>\n";
            }


            if (!(is_array($this->_fields))) {
                echo "non e un array!";
            }
//            echo "<pre>";
//            print_r($this->_fields);
//            echo "</pre>";
            foreach ($this->_fields as $key => $field) {

                if (empty($field)) {
                    $html .= "<br />\n";
                    continue;
                }
                $html = $field[0]->make($html);
            }
            if (isset($this->_formlabel) && !(empty($this->_formlabel))) {
                $html .= " </fieldset>\n";
            }
            $html .= "\n</form>";
            return $html;
        }

    }

}

?>