<?php

/*
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */

namespace WolfMVC\Template\Pagecomponent {

    /**
     * Genera il menu a lapidi. Accetta una serie di dati e richiede labels.
     * Parametri: data, labels, stonesinapage(10), stonesinarow(5),font-size (titolo=16px, altridati = 14px), w/h ratio(1,78)
     */
    class Gravestonesmenu extends \WolfMVC\Template\Pagecomponent {

        /**
         *
         * @readwrite
         */
        protected $_sizeInfo = array("w" => "0", "h" => "0");

        /**
         *
         * @readwrite
         */
        protected $_data = array();

        /**
         *
         * @readwrite
         */
        protected $_labels = array();

        /**
         * @readwrite
         * @var int Quante lapidi in una pagina
         */
        protected $_stonesinapage = 10;

        /**
         * @readwrite
         * @var int Quante lapidi in una riga
         */
        protected $_stonesinarow = 5;

        /**
         * @readwrite
         * @var array Font-size (titolo, altricampi)
         */
        protected $_fontsize = array(18, 14);

        /**
         * @readwrite
         * @var float Rapporto larghezza su alterzza 
         */
        protected $_whratio = 1.78;

        /**
         * @readwrite
         * @var array
         */
        protected $_gravestoneSize = array();

        /**
         * @readwrite
         * @var array Elenco ordinato campi da visualizzare sulla lapide
         */
        protected $_showinglabels = array();
        protected $_initialized = false;

        /**
         *
         * @var int Lunghezza della serie di dati (numero di righe)
         */
        protected $_datalength;

        /**
         *
         * @var Larghezza della serie di dati (numero di colonne)
         */
        protected $_datawidth;

        /**
         *
         * @var bool
         */
        protected $_render = true;
        protected $_scroll = array("w" => false, "h" => false);

        /**
         * @readwrite
         * @var string
         */
        protected $_onClick = "";

        protected function initialize() {
            foreach ($this->_showinglabels as $key => $label) { //controllo che i campi da mostrare siano tutti validi
                if (array_search($label, $this->_labels) === FALSE) {
                    unset($this->_showinglabels[$key]);
                }
            }
            if (!is_int($this->_fontsize[0]))
                $this->_fontsize[0] = 18;
            if (!is_int($this->_fontsize[1]))
                $this->_fontsize[1] = 14;
            if (isset($this->_sizeInfo["w"])) {
                if (!is_int($this->_sizeInfo["w"])) {
                    $this->_render = false;
                }
                if (!is_int($this->_sizeInfo["h"])) {
                    $this->_render = false;
                }
            }
            if (!(is_float($this->_whratio))) {
                $this->_whratio = 1.78;
            }
            if (!is_int($this->_stonesinapage))
                $this->_stonesinapage = 10;
            if (!is_int($this->_stonesinarow))
                $this->_stonesinarow = floor($this->_stonesinapage / 2);
            $this->_datalength = count($this->_data);
            $this->_datawidth = count($this->_labels);
            $this->_initialized = true;
        }

        protected function computeSize() {
            $ncampi = count($this->_showinglabels);
            $hstone = floor(3 * $this->_fontsize[0] + ($ncampi - 1) * $this->_fontsize[1]);
            $lstone = floor($hstone * $this->_whratio);
            $this->_gravestoneSize = array($hstone, $lstone);
            if (($lstone * $this->_stonesinarow) + ($lstone * 0.1 * ($this->_stonesinarow + 1)) > $this->_sizeInfo["w"])
                $this->_scroll["w"] = true;
            if ($hstone + ($this->_stonesinapage / $this->_stonesinarow * (1.1) + 1) > $this->_sizeInfo["h"])
                $this->_scroll["h"] = true;
            if (!$this->_scroll["w"]) {
                $this->_gravestoneSize[3] = floor(($this->_sizeInfo["w"] - ($this->_stonesinarow * $lstone)) / ($this->_stonesinarow + 1));
            } else {
                $this->_gravestoneSize[3] = floor($lstone / 10);
            }
            if (!$this->_scroll["h"]) {
                $this->_gravestoneSize[2] = floor(($this->_sizeInfo["h"] - (($this->_stonesinapage / $this->_stonesinarow) * $hstone)) / (($this->_stonesinapage / $this->_stonesinarow) + 1));
            } else {
                $this->_gravestoneSize[2] = floor($hstone / 10);
            }
        }

        public function render($html) {
            if (!$this->_initialized)
                $this->initialize();
            ob_start();
            $this->computeSize();
            $scroll = "";
            if ($this->_scroll["w"] == true) {
                $scroll .= " overflow-x: scroll;";
            }

            if ($this->_scroll["h"] == true) {
                $scroll .= " overflow-y: scroll;";
            }
            echo "<div class=\"gravestonesMenu\" style=\"display: block; width: " . $this->_sizeInfo["w"] . "px; height: " . $this->_sizeInfo["h"] . "px" . $scroll . "\">";
            echo $this->renderTitle();
            echo $this->renderNavigator();
            if ($this->_render) {

                $numpages = ceil($this->_datalength / $this->_stonesinapage);
                $gravestone = "<div id=\"gravestone_{{id}}\" class=\"gravestone\" style=\"border: 1px solid black; width: " . $this->_gravestoneSize[1] . "px; height: " . $this->_gravestoneSize[0] . "px; top: {{TOP}}px; left: {{LEFT}}px;\">{{CONTENT}}</div>";
                $nchar = floor(2 * ($this->_gravestoneSize[1] / ($this->_fontsize[1])) - 3);
                for ($pagenum = 0; $pagenum < $numpages; $pagenum ++) {

                    echo "<div class=\"gravestonesMenuPage\" style=\"border: 2px solid black; width: " . $this->_sizeInfo["w"] . "px; height: " . $this->_sizeInfo["h"] . "px;" . $scroll . "\">";
                    echo "<div class=\"gravestonesMenuPageNumber\" style=\"position: absolute; right: 20px; bottom: 20px;\">" . ($pagenum + 1) . "/" . $numpages . "</div>";
                    for ($lapide = 0; $lapide < $this->_stonesinapage; $lapide ++) {
                        $rownumber = ($pagenum + 1) * ($lapide + 1) - 1;

                        $id = $this->_parameters["Pk project"];
                        if (isset($this->_data[$rownumber])) {
                            foreach ($this->_labels as $labelk => $label) {
                                $id = str_replace("[[[" . $label . "]]]", $this->_data[$rownumber][$label], $id);
                            }
                        }

                        $numinrow = $lapide % $this->_stonesinarow;
                        $numincol = floor($lapide / $this->_stonesinarow);
                        $left = floor($this->_gravestoneSize[3]) * ($numinrow + 1) + floor($this->_gravestoneSize[1]) * ($numinrow);
                        $top = floor($this->_gravestoneSize[2]) * ($numincol + 1) + floor($this->_gravestoneSize[0]) * ($numincol);
                        $content = "";
                        $titolo = "Title";
                        if (!isset($this->_data[$rownumber])) {
                            continue;
                            $content = "";
                        } else {
                            foreach ($this->_showinglabels as $label) {
                                $stylefont = " font: " . ($titolo === "" ? $this->_fontsize[1] . "px;" : $this->_fontsize[0] . "px; font-weight: bold;");
                                $datum = $this->_data[$rownumber][$label];
                                if (strlen($datum) >= $nchar) {
                                    $datum = substr($datum, 0, $nchar);
                                    $datum .= "...";
                                }
                                $content .= "<span style=\"" . $stylefont . "\" class=\"gravestoneRow" . $titolo . "\">" . $datum . "</span><br>";
                                $titolo = "";
                            }
                        }
                        echo str_replace(array("{{TOP}}", "{{LEFT}}", "{{CONTENT}}", "{{id}}"), array($top, $left, $content, $id), $gravestone); //$numinrow." ".$left." ".$top
                    }
                    echo "</div>";
                }
            } else {
                echo "IMPOSSIBILE RENDERIZZARE";
            }
            echo "</div>";
            if ($this->_onClick !== "") {
                echo "<script>\nvar clickUrl = '" . $this->_onClick . "'\n</script>";
            }
            $out = ob_get_contents();
            ob_end_clean();
            return parent::render($out);
        }

        public function renderTitle() {
            ob_start();
            echo "<div class=\"gravestonesTitleInfo\">";
            if (isset($this->_titleInfo["title"]))
                echo "<p>" . $this->_titleInfo["title"] . "</p>";
            if (isset($this->_titleInfo["description"])) {
                echo "<p>" . $this->_titleInfo["description"] . "</p>";
            }

            echo "</div>";
            $out = ob_get_contents();
            ob_end_clean();
            return $out;
        }

        public function renderNavigator() {
            ob_start();
            echo "<div class=\"gravestonesNavLeft\"></div>";
            echo "<div class=\"gravestonesNavRight\"></div>";
            $out = ob_get_contents();
            ob_end_clean();
            return $out;
        }

        public function includingScript() {
            $out = array();
            array_push($out, "/gravestonesmenu/gravestonesmenu_main.js");
            return $out;
        }

        public function includingCss() {
            $out = array();
            array_push($out, "/gravestonesmenu/gravestonesmenu.css");
            return $out;
        }

    }

}
?>