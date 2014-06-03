/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */


function wolf_cens() {
    var tabs = $("table[orderingtable]");
    console.log("Tabelle ordinabili trovate:");
    console.log(tabs);
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
    console.log(ths);
    console.log("//////7");
    var thscont = $("#" + tableid + " th").map(function() {
        return $(this).html();
    });
    console.log(thscont);
    ths.each(function(i) {
//                        console.log($(this).attr('otdatatype'));
        var dt = $(this).attr('otdatatype');
        if (typeof (dt) === 'undefined')
            dt = "text";
        $(this).bind("click", function(event) {
            ordertable2(tableid, i, dt);
        });

//        $(this).html("<a href=\"javascript:ordertable2('" + tableid + "', " + i + ",'" + dt + "');\">" + thscont[i] + "</a>");
        console.log($(this).html());
//                        var dt = $(this).attr('otdatatype');
//                        var cont = $(this).attr('otdatatype');
//                        if (typeof (dt) === 'undefined')
//                            dt = "";
//                        switch(dt){
//                            case "date":
//                                
//                                break;
//                            default:
//                                
//                        }
    });
    var trs = table.find("tr:not(:eq(0))");
    console.log(trs);
    trs.each(function(i) {
        $(this).attr("wolfot_row", i);
    });
}


function ordertable2(tableid, col, flag) {
    if (typeof (flag) === 'undefined')
        flag = "";
    var table = $("#" + tableid);
    if (!table.length) {
        console.log("An error occurred. Can't find table: " + tableid);
        return;
    }
    var nrows = 0;
    var ncells = 0;
    var hrow = $("#" + tableid + " tr:eq(0)");
    var rows = $("#" + tableid + " tr:not(:eq(0))");
    var data = new Array();
    var format = new Array();
    nrows = rows.length;

    rows.each(function(i) {
//        console.log(i);
        data[i] = new Array();
        format[i] = new Array();
        data[i]["rowindex"] = $(this).attr("wolfot_row");
        data[i]["html"] = $(this).html();
        var tds = $(this).children("td");
        ncells = Math.max(ncells, tds.length);
        tds.each(function(j) {
            var txt = $(this).text();
            console.log(txt);
            if (((typeof (col.length) === "undefined") && (j == col))) {
                switch (flag) {
                    case "date":
                        var res = string2caldate($(this).text(), 0);
                        data[i][j] = res[1];
                        if ((typeof (format[i][j]) === 'undefined') || format[i][j] == null)
                            format[i][j] = new Array();
                        format[i][j]["dateformat"] = res[0];
                        break;
                    case "number":
                        data[i][j] = Number($(this).text());
                        break;

                    default:
                        data[i][j] = $(this).text();
                }
            }
            else if (index = jQuery.inArray(j, col) + 1) { //-1 => 0 = false, all others => >0 = true
                switch (flag[index]) {
                    case "date":
                        var res = string2caldate($(this).text(), 0);
                        data[i][j] = res[1];
                        if ((typeof (format[i][j]) === 'undefined') || format[i][j] == null)
                            format[i][j] = new Array();
                        format[i][j]["dateformat"] = res[0];
                        break;
                    case "number":
                        data[i][j] = Number($(this).text());
                        break;

                    default:
                        data[i][j] = $(this).text() + "";
                }

            }
        });
    });


    var sort = function(a, b) {
        if (typeof (a[col]) === "string" && typeof (b[col]) === "string") {
            return a[col].localeCompare(b[col]);
        }
        if (a[col] < b[col])
            return -1;
        else
            return 1;
    }
    var multisort = function(a, b) {

        if (!(jQuery.isArray(col))) {
            if (typeof (a[col]) === "string" && typeof (b[col]) === "string") {
                return a[col].localeCompare(b[col]);
            }
            if (a[col] < b[col])
                return -1;
            else
                return 1;
        }
        else {
            for (i = 0; i < col.length; i++) {
                if (typeof (a[col]) === "string" && typeof (b[col]) === "string") {
                    var comp = a[col].localeCompare(b[col])
                    if (comp != 0) {
                        return comp;
                    }
                    else {
                        continue;
                    }
                }
                if (a[col[i]] < b[col[i]])
                    return -1
                else if (a[col[i]] > b[col[i]])
                    return 1;
                else
                    continue;
            }
            return 1;
        }
        return 1;
    }
//    console.log(data);
//    console.log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>><sort");
    data.sort(sort);
//    console.log(data);

    var reorder = new Array();
    for (var i = 0; i < data.length; i++) {
        reorder[Number(data[Number(i)]['rowindex'])] = Number(i);
//        console.log('posizione ' + Number(data[i]['rowindex']) + " = " + Number(i));
    }
//    rows.detach();
//    console.log(data);
//    console.log(reorder);
//    for (var i = 0; i < data.length; i++) {
//        console.log(reorder[i]);
//        console.log(rows.eq(reorder[i]).html());
//    }
    for (var i = 0; i < data.length; i--) {
        console.log(rows.filter("tr[wolfot_row='" + reorder[i] + "']").find("td:eq(0)").html());

    }
    for (var i = data.length - 1; i >= 0; i--) {
        table.prepend(rows.filter("tr[wolfot_row='" + reorder[i] + "']"));
//        table.prepend(data[reorder[Number(i)]]["html"]);
    }
    table.prepend(hrow);
    return;

}

// prova
function swaprow(tableid) {
    var table = $("#" + tableid);
    console.log(table);
}