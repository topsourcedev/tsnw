
$(document).ready(function() {
    $(".MODElementFilterPanel").show("fold", 500);

    $(document).ready(function() {
        var menu = $("ul.MODElementFilterTokens");
        $(window).scroll(event, function() {
            menu.css({top: $(window).scrollTop()});
        });
    });

})

$(document).delegate(".MODElementFilterDelete", "click", function() {
    var filter = $(this).parents(".MODElementFilterPanelFilter");

    filter.empty().remove();
});
$(document).delegate(".MODElementFilterGroupDelete", "click", function() {
    var group = $(this).parents(".MODElementFilterPanelGroup");
    var groupsib = group.siblings(".MODElementFilterPanelFilter");
    if (groupsib.length === 0) {
        group.siblings(".MODElementFilterPanelNewGroup").eq(1).hide();
    }
    group.empty().remove();
});
$(document).delegate(".MODElementFilterButtonCancel", "click", function() {
    var filterpanel = $(".MODElementFilterPanel");
    var groups = filterpanel.find(".MODElementFilterPanelGroup");

    filterpanel.hide("fold", 1000);
    groups.remove();
});

$(document).delegate(".MODElementFilterButtonOk", "click", function() {
    var filterpanel = $(this).parents(".MODElementFilterPanel");
    var groups = filterpanel.find(".MODElementFilterPanelGroup");
    var filtersdata = new Array();
    var error = false;
    groups.each(function() {
        var filters = $(this).find(".MODElementFilterPanelFilter");
        var filtersslice = new Array();
        filters.each(function() {
            var filt = "{FIELD} ";
            if ($(this).find(".MODElementFilterOperator").val() === '-1') {
                $(this).find(".MODElementFilterOperator").css("background", "red");
                return;
            }
            filt += $(this).find(".MODElementFilterOperator").val();
            filt += " '" + $(this).find(".MODElementFilterField2Name").val() + "'";
            filtersslice.push(filt);
            var cong = $(this).find(".MODElementFilterPanelJoin");
            if (cong.length === 0) {
                var congiunzione = "AND";
            }
            else if (cong.html() === "AND"){
                var congiunzione = "AND";
            }
            else {
                var congiunzione = "OR";
            }
            filtersslice.push(congiunzione);
        });
        if (error) {
            return;
        }
        filtersslice.pop();
        filtersdata.push(filtersslice);
        var cong = $(this).find(".MODElementFilterGroupJoin");
            if (cong.length === 0) {
                var congiunzione = "AND";
            }
            else if (cong.html() === "AND"){
                var congiunzione = "AND";
            }
            else {
                var congiunzione = "OR";
            }
        filtersdata.push(congiunzione);
    });
    if (error)
        return;
    filtersdata.pop();
    console.log(filtersdata);
});
$(function() {
    $("li.MODElementFilterTokens").draggable({
        helper: "clone",
//        function(){
//                return $("<div></div>").attr("class","MODElementFilterPanelGroup")
//                        .html("Nuovo gruppo");
//            },
        cursor: "default",
        zIndex: 1000,
        start: function(ui, event) {
        },
        stop: function(ui, event) {
        }
//        connectToSortable: ".MODElementFilterPanelContainer"

    });

    $("ul.MODElementFilterTokens").disableSelection();
    $(".MODElementFilterPanelContainer").sortable({
        items: ".MODElementFilterPanelGroup",
        placeholder: "MODElementFilterPanelNewGroup"
    });
    $(".MODElementFilterPanelNewGroup:nth-child(1)").droppable({//drop su nuovo gruppo 1
        accept: "#MODElementFilterTokens_newgroup",
        greedy: true,
        tolerance: "touch",
        over: function(event, ui) {
            $(this).css("opacity", "1");
        },
        out: function(event, ui) {
            $(this).css("opacity", "0.5");
        },
        drop: function(event, ui) {
            console.log(event);
            console.log(ui);
            if (ui.draggable.attr("id") === "MODElementFilterTokens_newgroup") {
                var newgroup = $(".MODElementFilterPanelContainer")
                        .find(".MODElementFilterPanelGroupTemplate")
                        .clone()
                        .removeAttr("class")
                        .attr("class", "MODElementFilterPanelGroup")
                $(this).after(newgroup);
                newgroup.sortable({
                    items: ".MODElementFilterPanelFilter",
                    placeholder: "MODElementFilterPanelNewFilter"
                });
                newgroup.show("explode", 500);
                newgroup.find(".MODElementFilterPanelNewGroupJoin")
                        .droppable({//drop su nuova congiunzione gruppo
                            accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {

                                var newcond = $("<div>")
                                        .attr("class", "MODElementFilterPanelGroupJoin");

                                if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                    $(this).siblings(".MODElementFilterPanelGroupJoin").remove();
                                    $(this).before(newcond.html("AND"));
                                }
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                    $(this).siblings(".MODElementFilterPanelGroupJoin").remove();
                                    $(this).before(newcond.html("OR"));
                                }
                                newcond.show("explode", 500);

                            }
                        });
                newgroup.find(".MODElementFilterPanelNewFilter:nth-child(1)")
                        .droppable({//drop su nuovo filtro 1
                            accept: "#MODElementFilterTokens_newstatic",
                            greedy: true,
                            tolerance: "touch",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {
                                $(this).css("opacity", "0.5");
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                                    var newfilter = $(".MODElementFilterPanelContainer")
                                            .find(".MODElementFilterPanelFilterTemplate")
                                            .clone()
                                            .removeAttr("class")
                                            .attr("class", "MODElementFilterPanelFilter");
                                    $(this).after(newfilter);
                                    newfilter.show("explode", 500);
                                    newfilter.find(".MODElementFilterPanelNewFilterJoin")
                                            .droppable({
                                                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                                                over: function(event, ui) {
                                                    $(this).css("opacity", "1");
                                                },
                                                out: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                },
                                                drop: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                    var newcond = $("<div>")
                                                            .attr("class", "MODElementFilterPanelFilterJoin");

                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("AND"));
                                                    }
                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("OR"));
                                                    }
                                                    newcond.show("explode", 500);

                                                }
                                            });
                                    $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                                }
                                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                            }
                        });
                newgroup.find(".MODElementFilterPanelNewFilter:nth-child(2)")
                        .droppable({//drop su nuovo filtro 2
                            accept: "#MODElementFilterTokens_newstatic",
                            greedy: true,
                            tolerance: "touch",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {
                                $(this).css("opacity", "0.5");
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                                    var newfilter = $(".MODElementFilterPanelContainer")
                                            .find(".MODElementFilterPanelFilterTemplate")
                                            .clone()
                                            .removeAttr("class")
                                            .attr("class", "MODElementFilterPanelFilter");
                                    $(this).before(newfilter);
                                    newfilter.show("explode", 500);
                                    newfilter.find(".MODElementFilterPanelNewFilterJoin")
                                            .droppable({
                                                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                                                over: function(event, ui) {
                                                    $(this).css("opacity", "1");
                                                },
                                                out: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                },
                                                drop: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                    var newcond = $("<div>")
                                                            .attr("class", "MODElementFilterPanelFilterJoin");

                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("AND"));
                                                    }
                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("OR"));
                                                    }
                                                    newcond.show("explode", 500);

                                                }
                                            });
                                    $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                                }
                                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                            }
                        });

                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
            }
        }
    });
    $(".MODElementFilterPanelNewGroup:nth-child(2)").droppable({//drop su nuovo gruppo 2
        accept: "#MODElementFilterTokens_newgroup",
        greedy: true,
        tolerance: "touch",
        over: function(event, ui) {
            $(this).css("opacity", "1");
        },
        out: function(event, ui) {
            $(this).css("opacity", "0.5");
        },
        drop: function(event, ui) {
            if (ui.draggable.attr("id") === "MODElementFilterTokens_newgroup") {
                var newgroup = $(".MODElementFilterPanelContainer")
                        .find(".MODElementFilterPanelGroupTemplate")
                        .clone()
                        .removeAttr("class")
                        .attr("class", "MODElementFilterPanelGroup")
                $(this).before(newgroup);
                newgroup.sortable({
                    items: ".MODElementFilterPanelFilter",
                    placeholder: "MODElementFilterPanelNewFilter"
                });
                newgroup.show("explode", 500);
                newgroup.find(".MODElementFilterPanelNewGroupJoin")
                        .droppable({//drop su nuova congiunzione gruppo
                            accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {

                                var newcond = $("<div>")
                                        .attr("class", "MODElementFilterPanelGroupJoin");

                                if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                    $(this).siblings(".MODElementFilterPanelGroupJoin").remove();
                                    $(this).before(newcond.html("AND"));
                                }
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                    $(this).siblings(".MODElementFilterPanelGroupJoin").remove();
                                    $(this).before(newcond.html("OR"));
                                }
                                newcond.show("explode", 500);

                            }
                        });
                newgroup.find(".MODElementFilterPanelNewFilter:nth-child(1)")
                        .droppable({//drop su nuovo filtro 1
                            accept: "#MODElementFilterTokens_newstatic",
                            greedy: true,
                            tolerance: "touch",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {
                                $(this).css("opacity", "0.5");
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                                    var newfilter = $(".MODElementFilterPanelContainer")
                                            .find(".MODElementFilterPanelFilterTemplate")
                                            .clone()
                                            .removeAttr("class")
                                            .attr("class", "MODElementFilterPanelFilter");
                                    $(this).after(newfilter);
                                    newfilter.show("explode", 500);
                                    newfilter.find(".MODElementFilterPanelNewFilterJoin")
                                            .droppable({
                                                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                                                over: function(event, ui) {
                                                    $(this).css("opacity", "1");
                                                },
                                                out: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                },
                                                drop: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                    var newcond = $("<div>")
                                                            .attr("class", "MODElementFilterPanelFilterJoin");

                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("AND"));
                                                    }
                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("OR"));
                                                    }
                                                    newcond.show("explode", 500);

                                                }
                                            });
                                    $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                                }
                                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                            }
                        });
                newgroup.find(".MODElementFilterPanelNewFilter:nth-child(2)")
                        .droppable({//drop su nuovo filtro 2
                            accept: "#MODElementFilterTokens_newstatic",
                            greedy: true,
                            tolerance: "touch",
                            over: function(event, ui) {
                                $(this).css("opacity", "1");
                            },
                            out: function(event, ui) {
                                $(this).css("opacity", "0.5");
                            },
                            drop: function(event, ui) {
                                $(this).css("opacity", "0.5");
                                if (ui.draggable.attr("id") === "MODElementFilterTokens_newstatic") {
                                    var newfilter = $(".MODElementFilterPanelContainer")
                                            .find(".MODElementFilterPanelFilterTemplate")
                                            .clone()
                                            .removeAttr("class")
                                            .attr("class", "MODElementFilterPanelFilter");
                                    $(this).before(newfilter);
                                    newfilter.show("explode", 500);
                                    newfilter.find(".MODElementFilterPanelNewFilterJoin")
                                            .droppable({
                                                accept: "#MODElementFilterTokens_and, #MODElementFilterTokens_or",
                                                over: function(event, ui) {
                                                    $(this).css("opacity", "1");
                                                },
                                                out: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                },
                                                drop: function(event, ui) {
                                                    $(this).css("opacity", "0.5");
                                                    var newcond = $("<div>")
                                                            .attr("class", "MODElementFilterPanelFilterJoin");

                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_and") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("AND"));
                                                    }
                                                    if (ui.draggable.attr("id") === "MODElementFilterTokens_or") {
                                                        $(this).siblings(".MODElementFilterPanelFilterJoin").remove();
                                                        $(this).before(newcond.html("OR"));
                                                    }
                                                    newcond.show("explode", 500);

                                                }
                                            });
                                    $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                                }
                                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);
                            }
                        });

                $(this).siblings("." + $(this).attr("class").replace(" ", ".")).show(500);

            }
        }
    });
});