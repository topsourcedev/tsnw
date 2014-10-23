var app = angular.module("TSNWCLIENT", [], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
})
        .run(function ($rootScope) {
            $rootScope.tsnwdata = [];
        });
app.provider("ClientTSNW", function () {

    this.randomString = function (le)
    {
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";

        for (var i = 0; i < le; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }
    
    this.analyzeResponse = function(data, status, headers, config){
        
        return {data: data, status: status, headers: headers, config: config};
    }
    
    
    this.baseUrl = "https://ec2-54-213-213-176.us-west-2.compute.amazonaws.com/tsnwprerelease/public/tsnwapi/json";
    this.hash = [];
    this.$get = function ($http, $rootScope) {
        var that = this;
        return {
            show: function (requestIndex){
                return that.hash[requestIndex];
            },
            get: function (resource, id, query) {
                var flag = true;
                while (flag) {
                    var requestIndex = that.randomString(10);
                    if (typeof (that.hash[requestIndex]) === "undefined") {
                        flag = false;
                    }
                }
                that.hash[requestIndex] = requestIndex;
                var url = that.baseUrl;
                url += resource;
                if (typeof (id) !== "undefined" && id) {
                    url += "/" + id+".ws";
                }
                else if (typeof (query) === "object" && query) {
                    var qs = $.param(query);
                    url += ".ws?" + qs;
                }
                
                var responsePromise = $http.get(url)
                        .success(function (data, status, headers, config) {
                            that.hash[requestIndex] = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("getPerformed", [requestIndex]);
                        })
                        .error(function (data, status, headers, config) {
                            that.hash[requestIndex]  = that.analyzeResponse(data, status, headers, config);
                            $rootScope.$emit("getPerformed", [requestIndex]);
                        });
                return requestIndex;
            }
        }
    };
});


