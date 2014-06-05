/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


function wolf_cens() {
    var tabs = $("table[orderingtable]");
    tabs.each(function() {
        console.log($(this).attr('id'));
//                   var headtr = $("#"+$(this).attr('id')+" tr:eq(0)");
        var tableid = $(this).attr('id');
        if (typeof (tableid) === "undefined") {
            var rdnid = random_char_string_for_id(10, "wolfot_");
            $(this).prop('id', rdnid);
            tableid = rdnid;
        }
        //now i have a table that can be analyzed and has an id = tableid;
        //call initializer
        wolf_ot_initialize(tableid);

    });
}

function wolf_ot_initialize(tableid) {
    var table = $("#" + tableid);
    if (!table.length)
        return;
    var ths = $("#" + tableid + " th");
    var thscont = $("#" + tableid + " th").map(function() {
        return $(this).html();
    });
    ths.each(function(i) {
        var dt = $(this).attr('otdatatype');
        if (typeof (dt) === 'undefined')
            dt = "text";
//        $(this).bind("click", function(event) {
//            ordertable3(tableid, new Array(Number(i)), new Array(true), new Array(dt));
//        });
          

        $(this).html("<a href=\"javascript:ordertable('" + tableid + "', new Array('"+i+"') , new Array(true),new Array('" +dt + "'));\">" + thscont[i] + "</a>");
    });
    var trs = table.find("tr:not(:eq(0))");
    trs.each(function(i) {
        $(this).attr("wolfot_row", i);
    });
}


function ordertable(tableid, cols, ords, flags) {
    if ((typeof (cols.length) == "undefined") || (typeof (ords.length) == "undefined") || (typeof (flags.length) == "undefined")) {
        return; // cols, ords & flags must be array
    }
    if ((cols.length != ords.length) || (cols.length != flags.length)) {
        return;
    }
    for (var i = 0; i < cols.length; i++) {
        cols[i] = Number(cols[i]);
        ords[i] = (ords[i] == true);
    }
    var indexindex = Number(Math.max.apply(Math, cols) + 1);
    var data = new Array();
    var format = new Array();
    var table = $("#" + tableid);
    var hrow = table.find("tr:eq(0)");
    var intestazione = "";
    hrow.find("th").each(function() {
        intestazione += $(this).html() + " || ";
    })
    rows = table.find("tr:gt(0)");
    rows.each(function(i) {
        data[i] = new Array();
        for (var j = 0; j < cols.length; j++) {
            data[i][cols[j]] = $(this).find("td:eq(" + cols[j] + ")").html();
        }
        data[i][Number(indexindex)] = i;
    });
    var msort = function(a, b) {
        for (var i = 0; i < cols.length; i++) {
            var k = cols[i];
            if (ords[i]) { //ASC
                switch (flags[i]) {
                    case '':
                    case 'text':
                        var comp = strcomp(a[k],b[k]);
                        if (comp == 1)
                            return 1;
                        else
                            continue;
                        break;
                    case 'number':
                        if (Number(a[k]) > Number(b[k])) {
                            return 1;
                        }
                        break;
                    case 'date':
                        if (strdatecomp(a[k],b[k]) == 1) {
                            return 1;
                        }
                        break;
                    default:
                        if (a[k] > b[k]) {
                            return 1;
                        }
                }

            }
            else {
                switch (flags[i]) {
                    case '':
                    case 'text':
                        var comp = strcomp(a[k],b[k]);
                        if (comp == -1)
                            return 1;
                        else
                            continue;
                        break;
                    case 'number':
                        if (Number(a[k]) < Number(b[k])) {
                            return 1;
                        }
                        break;
                    case 'date':
                        if (strdatecomp(a[k],b[k]) == -1) {
                            return 1;
                        }
                        break;
                    default:
                        if (a[k] < b[k]) {
                            return 1;
                        }
                }

            }
        }
        return -1;
    }
    ;
    data.sort(msort)
//    console.log(data);

    for (var i = data.length - 1; i >= 0; i--) {
        table.prepend(rows.eq(data[i][indexindex]));
    }
    table.prepend(hrow);
}

function swaprow(tableid) {
    var table = $("#" + tableid);
    console.log(table);
}








function wolfot_generateTable(data){
    
}



function provavalori(){
    var test = new Array(null,"null","undefined",0,1,0.56,true,false,"","a","abc",new Array(),new Array(1), new Array(new Array()));
    var out = "";
    out += ("valore|var.length|typeof(var.length)")+"\n";
    out +=(test[0]+"|errore|errore")+"\n";
    for (var i= 1; i<test.length; i++){
        out +=(test[i]+"|"+test[i].length+"|"+typeof(test[i].length))+"\n";
    }
    console.log(out);
}