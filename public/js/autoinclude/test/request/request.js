var myres = angular.module('myresource', ['ngResource']);

myres.factory('Resource', ['$resource', function ($resource) {
        return function (url, params, methods) {
            var defaults = {
                update: {method: 'put', isArray: false},
                create: {method: 'post'}
            };

            methods = angular.extend(defaults, methods);

            var resource = $resource(url, params, methods);

            resource.prototype.$save = function () {
                if (!this.id) {
                    return this.$create();
                }
                else {
                    return this.$update();
                }
            };

            return resource;
        };
    }]);


var app = angular.module("RequestMod", ['ngResource'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
//app.config(['$resourceProvider', function ($resourceProvider) {
//        // Don't strip trailing slashes from calculated URLs
//        $resourceProvider.defaults.stripTrailingSlashes = false;
//    }]);
app.factory('Risorsa', ['$resource', function ($resource) {
        return $resource('https://ec2-54-213-213-176.us-west-2.compute.amazonaws.com/tsnwprerelease/public/tsnwapi/json/:id/:altroid.ws', 
        {id: '@id'},
        {update: {method: 'PUT'}});
    }]);
app.controller("MainController", function (Risorsa) {
    this.method = "GET";
    this.url = "https://ec2-54-213-213-176.us-west-2.compute.amazonaws.com/tsnwprerelease/public/tsnwapi/json/:id/:altroid.ws";
    this.payload = {id: 27, chiave: "ciao", altrachiave: 11};
    this.response = {};
    var pippo = this;
    this.sendRequest = function () {
        switch (pippo.method) {
            case 'RETRIEVE':
                var prova = Risorsa.get(pippo.payload, function (successResult) {
                    pippo.response = successResult;
                }, function (errorResult) {
                    pippo.response = errorResult;
                });
                break;
            case 'CREATE':
                var prova = Risorsa.save(pippo.payload,pippo.payload, function (successResult) {
                    pippo.response = successResult;
                }, function (errorResult) {
                    pippo.response = errorResult;
                });
                break;
            case 'UPDATE':
                var prova = Risorsa.update(pippo.payload,pippo.payload, function (successResult) {
                    pippo.response = successResult;
                }, function (errorResult) {
                    pippo.response = errorResult;
                });
                break;
            case 'DELETE':
                var prova = Risorsa.delete(pippo.payload, function (successResult) {
                    pippo.response = successResult;
                }, function (errorResult) {
                    pippo.response = errorResult;
                });
                break;
        }
    };
});

