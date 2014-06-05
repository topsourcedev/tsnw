function ordertable3(tableid, cols, ords, flags) {
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
    console.log(cols);
    console.log(ords);
    console.log("----------------------");
    var indexindex = Number(Math.max.apply(Math, cols) + 1);
    console.log(indexindex);
    var data = new Array();
    var format = new Array();
    var table = $("#" + tableid);
//    console.log(table);
    var hrow = table.find("tr:eq(0)");
//    console.log(hrow);
    var intestazione = "";
    hrow.find("th").each(function() {
        intestazione += $(this).html() + " || ";
    })
//    console.log(intestazione);
    rows = table.find("tr:gt(0)");
//    console.log(rows);
    rows.each(function(i) {
//        console.log($(this).find("td:eq(0)").html());
        data[i] = new Array();
        for (var j = 0; j < cols.length; j++) {
            data[i][cols[j]] = $(this).find("td:eq(" + cols[j] + ")").html();
        }
        data[i][Number(indexindex)] = i;
    });
    console.log(data);
    var msort = function(a, b) {
        console.log(a);
        console.log("VS");
        console.log(b);
        for (var i = 0; i < cols.length; i++) {
            var k = cols[i];
            if (ords[i]) { //ASC
                switch (flags[i]) {
                    case '':
                    case 'text':
                        console.log(a[k] + " VS " + b[k]);
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
                        console.log(string2caldate(a[k],0));
                        console.log("VS");
                        console.log(string2caldate(b[k],0));
                        if (strdatecomp(a[k],b[k]) == 1) {
                            console.log(string2caldate(a[k],0) +">"+ string2caldate(b[k],0))
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
                        console.log(a[k] + " VS " + b[k]);
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

    console.log("-----------------------------");
    var reorder = new Array();
    for (var i = data.length - 1; i >= 0; i--) {
        table.prepend(rows.eq(data[i][indexindex]));
//        table.prepend(rows.filter("tr[wolfot_row='" + data[i][indexindex] + "']"));
    }
    table.prepend(hrow);
}