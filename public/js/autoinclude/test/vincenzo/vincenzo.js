
var app = angular.module("Vincenzo", ['TSNWCLIENT'], function ($interpolateProvider) {
    $interpolateProvider.startSymbol("--__");
    $interpolateProvider.endSymbol("__--");
});
app.controller("MainController", function (ClientTSNW, $rootScope) {
    this.so = [];
    this.resource = "/accounts/432/project";
    this.resourceid = "543";
    this.payload = [];
    this.payload[0] = {"chiave": "projectname", "valore": "ciao"};

    this.adjustPayload = function () {
        if (this.payload.length === 1) {
            if (this.payload[0].chiave !== "" || this.payload[0].valore !== "")
                this.payload[1] = {"chiave": "", "valore": ""};
        }
        else {
            var le = this.payload.length;
            if (this.payload[le - 1].chiave !== "" || this.payload[le - 1].valore !== "")
                this.payload[le] = {"chiave": "", "valore": ""};
        }
    }
    var pippo = this;
    this.sendRequest = function () {
        var payload = {};
        for (var i in pippo.payload){
            if (pippo.payload[i].chiave === "" && pippo.payload[i].valore === ""){
                pippo.payload.splice(i, 1);
            }
            else {
                payload[pippo.payload[i].chiave] = pippo.payload[i].valore;
            }
        }
        if (pippo.payload.length === 0){
            pippo.payload[0] = {"chiave": "", "valore": ""};
        }
        var req = ClientTSNW.get(pippo.resource, pippo.resourceid, payload);
        $rootScope.$on("getPerformed", function (event, mass) {
            if (req === mass[0]) {
                pippo.so = ClientTSNW.show(mass[0]);
            }
        });

    };

}
);