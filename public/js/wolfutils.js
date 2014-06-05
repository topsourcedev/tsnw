/* 
 * Questo software è stato creato da Alberto Brudaglio per TopSource S.r.l. Tutti i diritti sono riservati.
 * This software has ben developed by Alberto Brudaglio for Topsource S.r.l. All rights reserved.
 */
// return a random alphabetic string of length ll;
function random_char_string(ll) {
    if (Number(ll) <= 0)
        return "";
    var res = "";
    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    for (var i = 0; i < ll; i++) {
//        console.log(chars[Math.round((chars.length - 1) * Math.random())]);
        res += chars.charAt(Math.round((chars.length - 1) * Math.random()));
    }
    return res;
}

function random_char_string_for_id(ll, prefix, postfix) {
    if (Number(ll) <= 0)
        return "";
    if (typeof (prefix) === "undefined") {
        prefix = "";
    }
    if (typeof (postfix) === "undefined") {
        postfix = "";
    }


    var chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
    var flag = true; //
    while (flag) {
        var res = "";
        for (var i = 0; i < ll; i++) {
//            console.log(chars[Math.round((chars.length - 1) * Math.random())]);
            res += chars.charAt(Math.round((chars.length - 1) * Math.random()));
        }
        flag = document.getElementById(prefix + res + postfix);
    }
    return prefix + res + postfix;
}


function caldate2string(date, flag) {
    //date must be of type date
    //flag must be of type (1/2), 1=yyyy-mm-dd, 2=dd-mm-yyyy
    flag = Number(flag);
    if (date instanceof Date && !isNaN(date.valueOf()) && !isNaN(flag) && (flag >= 1) && (flag <= 2)) {
        var dd = date.getDate();
        if (dd < 10)
            dd = "0" + dd;
        var mm = 1 + Number(date.getMonth());
        if (mm < 10)
            mm = "0" + mm;
        var yy = date.getFullYear();
        if (yy < 10)
            yy = "000" + yy;
        else if (yy < 100)
            yy = "00" + yy;
        else if (yy < 1000)
            yy = "0" + yy;
        if (flag == 1)
            return yy + "-" + mm + "-" + dd;
        else
            return dd + "-" + mm + "-" + yy;
    }
    else
    {
        //TODO :: should throw an exception!
        return date;
    }
}

function string2caldate(string, flag) {
    //string must be dd-mm-yyyy or yyyy-mm-dd
    //flag must be of type (0/1/2), 0=auto, 1=yyyy-mm-dd, 2=dd-mm-yyyy
    flag = Number(flag);
    if (string.length == 10) {
        switch (flag) {
            case 0:
            case '0':
                //try to understand format
                var array = string.split("-");
                if (array.length != 3) {
                    return new Array(-1, string); //TODO:: throw exception!
                }
                else {
                    if ((array[0].length == 2) && (array[1].length == 2) && (array[2].length == 4)) {
                        return new Array(2, new Date(array[2] + "-" + array[1] + "-" + array[0]));
                    }
                    else if ((array[0].length == 4) && (array[1].length == 2) && (array[2].length == 2)) {
                        return new Array(1, new Date(string));
                    }
                }
                break;
            case 1:
            case '1':

                return new Array(1, new Date(string));
                break;
            case 2:
            case '2':
                var array = string.split("-");
                return new Array(2, Date(array[2] + "-" + array[1] + "-" + array[0]));
                break;

        }
    }
}

function strcomp(s1, s2) {
    var minlen = Math.min(s1.length, s2.length);
    var div = false;
    for (var i = 0; i < minlen; i++) {
        if (s1.charCodeAt(i) === s2.charCodeAt(i)) {
            continue;
        }
        else if (s1.charCodeAt(i) > s2.charCodeAt(i)) {
            return 1;
        }
        else if (s1.charCodeAt(i) < s2.charCodeAt(i)) {
            return -1;
        }
    }
    if (!(div)) {
        if (s1.length > s2.length) {
            return 1;
        }
        else if (s1.length == s2.length)
            return 0;
    }
    return -1;
}

function strdatecomp(s1, s2) {
    // prende due stringhe del tipo cccc-cc-cc oppure cc-cc-cccc
    //capisce il formato e le confronta come date

    //provo ita:
    if (s1 == "") 
        s1 = "00-00-0000";
    if (s2 == "") 
        s2 = "00-00-0000";
    var match1 = s1.match(/(\d{2})-(\d{2})-(\d{4})/);
    if (match1 != null && typeof (match1.length) != "undefined" && match1.length == 4) {
        var giorno1 = match1[1];
        var mese1 = match1[2];
        var anno1 = match1[3];
    }
    else {
        var match1 = s1.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match1 != null && typeof (match1.length) != "undefined" && match1.length == 4) {
            var anno1 = match1[1];
            var mese1 = match1[2];
            var giorno1 = match1[3];
        }
        else{
            return 0;
        }
    }
    
    var match2 = s2.match(/(\d{2})-(\d{2})-(\d{4})/);
    if (match2 != null && typeof (match2.length) != "undefined" && match2.length == 4) {
        var giorno2 = match2[1];
        var mese2 = match2[2];
        var anno2 = match2[3];
    }
    else {
        var match2 = s2.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match2 != null && typeof (match2.length) != "undefined" && match2.length == 4) {
            var anno2 = match2[1];
            var mese2 = match2[2];
            var giorno2 = match2[3];
        }
        else{
            return 0;
        }
    }
    
    if (anno1 == anno2){
        if (mese1 == mese2){
            if (giorno1 == giorno2){
                return 0;
            }
            else if (giorno1 < giorno2){
                return -1
            }
            else{
                return 1;
            }
        }
        else if (mese1 < mese2){
            return -1
        }
        else {
            return 1;
        }
    }
    else if (anno1 < anno2){
        return -1;
    }
    else{
        return 1;
    }
}