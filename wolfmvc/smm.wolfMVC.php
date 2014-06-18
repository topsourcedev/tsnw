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
         * @read
         * @var array I campi di cui è composto un dato
         * Struttura: array: un elemento per ogni campo. Ogni elemento è un array fatto come segue:
         * [nome_campo, etichetta_campo, 
         */
        protected $_fields;
        
        
        // imposta il campo identificativo
        public function identifyBy(){
            
        }
        
        // aggiunge un campo diretto principale
        public function addDirectPrincipal(){
            
        }
        // aggiunge un campo diretto attributivo
        public function addDirectAttributive(){
            
        }
        // aggiunge un campo diretto di dettaglio
        public function addDirectListing(){
            
        }
        //aggiunge un campo riferimento parallelo
        public function addReferenceParallel(){
            
        }
        //aggiunge un campo riferimento derivato
        public function addReferenceDerived(){
            
        }
        //aggiunge un campo riferimento di dettaglio
        public function addReferenceListing(){
            
        }
    }

}
