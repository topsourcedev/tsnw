/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


function solvetable(model, hash, pathtoparent, path, mult) {
    var node = model;
    var p = "";
    var pp = "";
    var parent = model;
    for (var i in pathtoparent) {
        pp += "->" + pathtoparent[i];
        parent = parent[pathtoparent[i]];
    }
    for (var i in path) {
        p += "->" + path[i];
        node = node[path[i]];
    }

    var nodename = path[path.length - 1];
    var parentname = pathtoparent[pathtoparent.length - 1];
    console.log(p);
    console.log(pp);
    console.log(parent);
    console.log(node);
    console.log(nodename + " figlio di " + parentname);
    var children = 0;
    for (var j in node) {
        console.log("figlio " + j);
        if (node[j] === "")
            continue;
        var newpath = path.concat(new Array(j));
        var newppath = path;
        solvetable(model, hash, newppath, newpath);
        children++;
    }
    if (children > 0) {
        node.haschildren = true;

    }
    else {
        node.haschildren = false; // sono nella tabella foglia!!!!

    }

    switch (nodename) {
        case "structure":
            var TABLES = "";
            var FIELDS = new Array();
            if (typeof (node.tabs) === "undefined")
                break;
            if (typeof (node.fies) === "undefined")
                node.fies = new Array();
            var mainalias = model.structure[model.maintable].tabs[0].alias;
            for (var i in node.tabs) {
                if (node.tabs[i].subqueried === false) {
                    if (typeof (node.tabs[i].sql) === "undefined" || node.tabs[i].sql === "") {
                        node.tabs[i].sql = "LEFT JOIN " + node.tabs[i].tablename + " " + node.tabs[i].alias + " ON (" + node.tabs[i].alias + "." + node.tabs[i].fieldto + " = " + mainalias + "." + node.tabs[i].fieldfrom + ")";
                    }
//                
                    TABLES += node.tabs[i].sql;
                }
            }
            for (var jj in model.fields) {
                var fieldname = model.fields[jj].fieldname;
                for (var ii in node.fies) {
                    if (node.fies[ii].fieldname !== fieldname)
                        continue;
                    if (node.fies[ii].aliased) {
                        FIELDS.push(node.fies[ii].tablealias + "." + node.fies[ii].tmpalias + " as '" + node.fies[ii].defalias + "'");
                    }
                    else {
                        FIELDS.push(node.fies[ii].tablealias + "." + node.fies[ii].fieldname + " as '" + node.fies[ii].defalias + "'");
                    }
//                
                }
            }
//            
            var FIELDS2 = new Array();
            for (var fgh in FIELDS) {
                if (FIELDS2.indexOf(FIELDS[fgh]) === -1)
                    FIELDS2.push(FIELDS[fgh]);
            }
            FIELDS2 = FIELDS2.join(",");
            model.select = model.select.replace("#FIELDS#", FIELDS2);
            model.select = model.select.replace("#TABLES#", TABLES);
            break;
        case "onetoone":
            if (typeof (node.tabs) === "undefined")
                break;
            if (typeof (parent.tabs) === "undefined")
                parent.tabs = new Array();
            if (typeof (node.fies) === "undefined")
                node.fies = new Array();
            if (typeof (parent.fies) === "undefined")
                parent.fies = new Array();

            for (var i in node.tabs) {
                if (typeof (node.tabs[i].sql) === "undefined") {
                    node.tabs[i].sql = "LEFT JOIN " + node.tabs[i].tablename + " " + node.tabs[i].alias + " ON (" + node.tabs[i].alias + "." + node.tabs[i].fieldto + " = " + "#MOTHERTABLEALIAS#" + "." + node.tabs[i].fieldfrom + ")"
                }
                parent.tabs.push(node.tabs[i]);
            }
            for (var i in node.fies) {
                parent.fies.push(node.fies[i]);
            }
            break;
        case "manytoone":
            if (typeof (node.tabs) === "undefined")
                break;
            if (typeof (parent.tabs) === "undefined")
                parent.tabs = new Array();
            if (typeof (node.fies) === "undefined")
                node.fies = new Array();
            if (typeof (parent.fies) === "undefined")
                parent.fies = new Array();
            for (var i in node.tabs) {
                if (typeof (node.tabs[i].sql) === "undefined") {
                    node.tabs[i].sql = "LEFT JOIN " + node.tabs[i].tablename + " " + node.tabs[i].alias + " ON (" + node.tabs[i].alias + "." + node.tabs[i].fieldto + " = " + "#MOTHERTABLEALIAS#" + "." + node.tabs[i].fieldfrom + ")"
                }
                parent.tabs.push(node.tabs[i]);
            }
            for (var i in node.fies) {
                parent.fies.push(node.fies[i]);
            }
            break;
        case "onetomany":
            console.log(">>>>>>>>>>>>>>>>>>>>>>>>onetomany");
            if (typeof (node.tabs) === "undefined")
                break;
            if (typeof (parent.tabs) === "undefined")
                parent.tabs = new Array();
            if (typeof (node.fies) === "undefined")
                node.fies = new Array();
            if (typeof (parent.fies) === "undefined")
                parent.fies = new Array();

            console.log("devo capire quante relazioni 1:n distinte ho in questo livello");
            var lastins = new Array();
            for (var i in node.tabs) {
                pexp = node.tabs[i].path.split("->");
                plast = pexp[pexp.length - 1];
                console.log("cerco nodi tabella " + plast);
                if (typeof (node[plast]) !== "undefined") {
                    console.log("trovato");
                    console.log(node[plast]);
                    node.tabs[i].state = "appena inserito";
                    lastins.push(i);
                }
                else {
                    node.tabs[i].state = "";
                }
            }
            for (var j in lastins) {
                console.log("Lavoro su tabella " + lastins[j] + " ovvero:");
                console.log(node.tabs[lastins[j]]);
                while (true) {
                    var alias = rdnstring(2);
                    if (typeof (hash[alias]) === "undefined") {
                        hash[alias] = alias;
                        break;
                    }
                }
                while (true) {
                    var joinalias = rdnstring(3);
                    if (typeof (hash[joinalias]) === "undefined") {
                        hash[joinalias] = joinalias;
                        break;
                    }
                }
                var fields = new Array();

                console.log("creo subquery");
                var sq = "LEFT JOIN (SELECT #FIELDS# FROM ";
                sq += node.tabs[lastins[j]].tablename + " " + node.tabs[lastins[j]].alias + " ";
                console.log(sq);
                for (var i in node.tabs) {
                    console.log("scorro altre tabelle");
                    if (i === lastins[j]) {
                        console.log("tabella stessa");
                        continue;
                    }
//                    var tmppath = node.tabs[i].path;
                    if ((node.tabs[i].path.indexOf(node.tabs[lastins[j]].path) === 0) && (node.tabs[i].sql === "")) {
                        console.log("la tabella");
                        console.log(node.tabs[i]);
                        console.log("non ha sql, quindi la trasformo in un l.j");
                        sq += "LEFT JOIN " + node.tabs[i].tablename + " " + node.tabs[i].alias + " ON (" + node.tabs[i].alias + "." + node.tabs[i].fieldto + "=" + node.tabs[lastins[j]].alias + "." + node.tabs[i].fieldfrom + ") ";
                        node.tabs[i].subqueried = true;
                    }
                    else if ((node.tabs[i].path.indexOf(node.tabs[lastins[j]].path) === 0) && (node.tabs[i].sql !== "")) {
                        console.log("la tabella");
                        console.log(node.tabs[i]);
                        console.log("ha sql, quindi la ricopio");
                        sq += node.tabs[i].sql;
                        node.tabs[i].subqueried = true;
                    }
                }
                sq += "GROUP BY " + node.tabs[lastins[j]].alias + "." + node.tabs[lastins[j]].fieldto;
                sq += ") " + alias + " ON (" + alias + "." + joinalias + " = #MOTHERTABLEALIAS#." + node.tabs[lastins[j]].fieldfrom + ") ";
                console.log("analizzo campi");
                for (var ffj in node.fies) {
                    var fieldpath = node.fies[ffj].path;
                    fieldpath = fieldpath.split("->");
                    var lastfieldpath = fieldpath[fieldpath.length - 1]; // questo è il nome interno della tabella che ha introdotto questo campo
                    var hasid = model.tables[lastfieldpath].hasid;
                    var id = model.tables[lastfieldpath].id;
                    console.log(lastfieldpath);
                    console.log(node.fies[ffj].defalias);
                    if (node.fies[ffj].path.indexOf(node.tabs[lastins[j]].path) === 0) {
                        if (node.fies[ffj].aliased === false) { //è un campo appena inserito
//                            if (hasid) {
//                                fields.push("CONCAT('{',GROUP_CONCAT(CONCAT(" + node.fies[ffj].tablealias + "." + id + ",':', " + node.fies[ffj].tablealias + "." + node.fies[ffj].fieldname + ")),'}')" + " as " + node.fies[ffj].tmpalias);
//                            }
//                            else {
                                fields.push("CONCAT('{',GROUP_CONCAT(" + node.fies[ffj].tablealias + "." + node.fies[ffj].fieldname + "),'}')" + " as " + node.fies[ffj].tmpalias);
//                            }
                            node.fies[ffj].aliased = true;
                            node.fies[ffj].tablealias = alias;
                        }
                        else {
                            while (true) {
                                var newalias = rdnstring(5);
                                if (typeof (hash[newalias]) === "undefined") {
                                    hash[newalias] = newalias;
                                    break;
                                }
                            }
//                            if (hasid) {
//                                fields.push("CONCAT('{',GROUP_CONCAT(CONCAT(" + node.fies[ffj].tablealias + "." + id + ",':', " + node.fies[ffj].tablealias + "." + node.fies[ffj].tmpalias + ")),'}') as " + newalias);
//                            }
//                            else {
                                fields.push("CONCAT('{',GROUP_CONCAT(" + node.fies[ffj].tablealias + "." + node.fies[ffj].tmpalias + "),'}') as " + newalias);
//                            }
//                            fields.push("CONCAT('{',GROUP_CONCAT(" + node.fies[ffj].tablealias + "." + node.fies[ffj].tmpalias + "),'}') as " + newalias);
                            node.fies[ffj].tmpalias = newalias;
                            node.fies[ffj].aliased = true;
                            node.fies[ffj].tablealias = alias;

                        }

                        node.fies[ffj].aliased = true;
                    }
                }
                fields.unshift(node.tabs[lastins[j]].alias + "." + node.tabs[lastins[j]].fieldto + " as " + joinalias);
                fields = fields.join(", ");
                sq = sq.replace("#FIELDS#", fields);
                node.tabs[lastins[j]].sql = sq;
                parent.tabs.push(node.tabs[lastins[j]]);
            }
            for (var i in node.fies) {
                parent.fies.push(node.fies[i]);
            }
            console.log(">>>>>>>>>>>>>>>>>>>>>>>>fine onetomany");
            break;
        default: //caso nome tabella
            if (typeof (node.tabs) === "undefined")
                node.tabs = new Array();
            if (typeof (parent.tabs) === "undefined")
                parent.tabs = new Array();
            if (typeof (node.fies) === "undefined")
                node.fies = new Array();
            if (typeof (parent.fies) === "undefined")
                parent.fies = new Array();
            while (true) {
                var alias = rdnstring(2);
                if (typeof (hash[alias]) === "undefined") {
                    hash[alias] = alias;
                    break;
                }
            }
            for (var kk in node.tabs) {
                if (typeof (node.tabs[kk].sql) === "string") {
                    node.tabs[kk].sql = node.tabs[kk].sql.replace("#MOTHERTABLEALIAS#", alias);
                }
            }
            if (nodename === model.maintable) {
                node.tabs.unshift({
                    tablename: model.tables[nodename].tablename,
                    fieldto: model.tables[nodename].fieldto,
                    fieldfrom: model.tables[nodename].fieldfrom,
                    partialmatching: model.tables[nodename].partialmatching,
                    alias: alias,
                    path: p,
                    sql: model.tables[nodename].tablename + " " + alias + " ",
                    subqueried: false
                });
                for (var ss in node.tabs) {
                    node.tabs[ss].sql = node.tabs[ss].sql.replace("#MOTHERTABLEALIAS", alias);
                }
            }
            else {
                node.tabs.unshift({
                    tablename: model.tables[nodename].tablename,
                    fieldto: model.tables[nodename].fieldto,
                    fieldfrom: model.tables[nodename].fieldfrom,
                    partialmatching: model.tables[nodename].partialmatching,
                    alias: alias,
                    path: p,
                    sql: "",
                    subqueried: false
                });
            }
            for (var hh in node.tabs) {
                parent.tabs.push(node.tabs[hh])
            }
            var fields = model.tables[nodename].fields;
            if (typeof (fields) === "undefined")
                fields = new Array();
            else
                fields = fields.split("|");
            for (var ffi in fields) {
                while (true) {
                    var fieldalias = rdnstring(5);
                    if (typeof (hash[fieldalias]) === "undefined") {
                        hash[fieldalias] = fieldalias;
                        break;
                    }
                }
                node.fies.push({
                    fieldname: model.fields[fields[ffi]].fieldname,
                    defalias: model.fields[fields[ffi]].alias,
                    tmpalias: fieldalias,
                    path: p,
                    aliased: false,
                    tablealias: alias
                });
            }
            for (var hh in node.fies) {
                parent.fies.push(node.fies[hh])
            }
    }

    return true;
}

