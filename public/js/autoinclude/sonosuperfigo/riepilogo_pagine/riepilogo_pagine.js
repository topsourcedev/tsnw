(function() {
    var app = angular.module("RiepilogoPagine", ['ui.bootstrap', 'DATAMODULE'], function($interpolateProvider) {
        $interpolateProvider.startSymbol("--__");
        $interpolateProvider.endSymbol("__--");
    });
    app.run(function() {

    });
    app.filter("showOnlyViews", function() {
        return function(input) {
            var ret = [];
            for (var i in input) {
                if (typeof (input[i]['view']) !== "undefined" && input[i]['view'] !== "") {
                    ret.push(input[i]);
                }
            }
            return ret;
        }
    });


    app.controller("MainController", function($timeout, ConfiguratorService, GetDataService) {
        var url = document.URL + ".ws";
        ConfiguratorService.get(url);
        this.pagine = {};
        this.pagineLoaded = {};

        this.showOnlyViews = true;

        var pippo = this;
        this.load = function(force) {
            if (typeof(force) === "undefined" || force || pippo.pagineLoaded === {}) {
                
                GetDataService("getElencoPagine", this.pagine, this.pagineLoaded);
            }
        }
        $timeout(function(){pippo.load();},1000);


    });

})();


