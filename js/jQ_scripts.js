$(function() {
    $( "#sortable" ).sortable({
        placeholder: "ui-state-highlight"
    });
    $( "#sortable" ).disableSelection();
});
$(document).ready(function(){
    
    var icon_pencil = '<span class="icon-container floatr ui-corner-all"><span class="ui-icon ui-icon-pencil"></span></span>';     
    var icon_plus = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-plusthick"></span></span>';     
    var icon_accept = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-check"></span></span>';     
    var icon_cancel = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-cancel"></span></span>';     
    var icon_delete = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-close"></span></span>';     
    
    
    $( "#ul_vystupy, #ul_aktivity" ).sortable({
        placeholder: "ui-state-highlight"
    });
    $( "#ul_vystupy, #ul_aktivity" ).disableSelection();
    
    
    
    $("table.matrix h2")
        .append(icon_plus)
        .addClass("ui-widget-header ui-corner-all")
        .hover(
            function(){$(this).find(".icon-container").show(200)},
            function(){$(this).find(".icon-container").hide(200)}
        )
    ;
    
    $("#zamer h2, #cil h2")
//        .append('<span class="icon-container floatr ui-corner-all"></span>')
        .append(icon_pencil)
    ;
    
    /* Odkazy horni nabidky a radky matice*/
    $("table.matrix ul li,#top ul li").addClass("ui-state-default")
    .hover(
        function() {
            $(this).addClass('ui-state-hover').addClass('color_white');
        }, 
        function() {
            $(this).removeClass('ui-state-hover').removeClass('color_white');
        }
        );
    $("table.matrix ul#ul_vystupy li")
    .hover(
        function() {
            var id = $(this).attr("id");
            $("#ul_aktivity li."+id).addClass('ui-state-hover');
        }, 
        function() {
            var id = $(this).attr("id");
            $("#ul_aktivity li."+id).removeClass('ui-state-hover');
        }
        );
    $("table.matrix ul#ul_vystupy li input:checkbox")
    .attr("checked",true)
    .change(
        function() { 
            $(this).parents("ul").find("input:checkbox").each(function(){
                var id = $(this).parent().attr("id");
                if ($(this).attr("checked")){
                    $("#ul_aktivity li."+id).show();    
                } else {
                    $("#ul_aktivity li."+id).hide();    
                }
            });

        }
        );
    
    /*
     * Vystupy
     */
    $("#vystupy li")
        .append(icon_pencil + icon_delete)
        .hover(function(){
            $(this).find(".icon-container").show(200);
        }, function(){
            $(this).find(".icon-container").hide(200);
        })
        .each(function(){
            $(this).find(".ui-icon-close").click(function(){
//                var val = $("#inserting_input").attr("value");
                var id = $(this).parents("li").attr("id");
                $("#dialog")
                .text("Ted by se vystup \""+id+"\" smazal z DB")
                .attr("title", "Smazani vystupu")
                .dialog({
                    modal: true,
                    buttons: {
                        Ok: function() {
                            $( this ).dialog( "close" );
                        }
                    }
                });
            });
            $(this).find(".ui-icon-pencil").click(function(){
//                var val = $("#inserting_input").attr("value");
                var text = $(this).parents("li").text();
                $(this).parents("li").html('<input id="editing_value" type="text" value="'+text+'"/>'+icon_accept + icon_cancel);
//                $("#dialog")
//                .text(text)
//                .attr("title", "Text")
//                .dialog({
//                    modal: true,
//                    buttons: {
//                        Ok: function() {
//                            $( this ).dialog( "close" );
//                        }
//                    }
//                });
            });
        })
        ;
    
                    
    /*********************/
    /*****  Ikonky *******/
    /*********************/
    
//    $('.ui-icon').hover(
//            function() {
//                $(this).addClass('ui-state-hover');
//            }, 
//            function() {
//                $(this).removeClass('ui-state-hover');
//            });
    $('.icon-container')
        .hover(
            function() {
                $(this).addClass('ui-state-hover');
            }, 
            function() {
                $(this).removeClass('ui-state-hover');
            })
        //                    $(".icon-add").hover(function(){$(this).addClass("icon-add-hover");},
        //                                        function(){$(this).removeClass("icon-add-hover");})
        ;
    $('.ui-icon-plusthick')
        .click(function(){
            // najde, k cemu pridat li
            $(this).parents("td").find("ul")
            .append('<li class="ui-state-default">'+
                '<input type="text" id="inserting_input" class="edit_input"/>'+
                icon_accept+icon_cancel+
                '</li>')
            .find(".icon-container")
                .show()
                .hover(
                    function(){$(this).addClass("ui-state-hover")},
                    function(){$(this).removeClass("ui-state-hover")})
                .click(function(){
                    var val = $("#inserting_input").attr("value");
                    $("#dialog")
                    .text("Ted by se vlozila hodnota: "+val+" do DB")
                    .attr("title", "Ulozeni nove hodnoty do DB")
                    .dialog({
                        modal: true,
                        buttons: {
                            Ok: function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    $(this).parents('li').remove();
                });
            
            $(this).parents("td").find(".ui-icon-cancel").click(function(){
                $(this).parents('li').remove();
            });
//            $(this).parents("td").find(".icon-cancel")
//            .hover(function(){
//                $(this).addClass("icon-cancel-hover")
//                }, function(){
//                $(this).removeClass("icon-cancel-hover")
//                })
//            .click(function(){
//                $(this).parents('li').remove();
//            });

            $("#inserting_input").focus();
        });
});