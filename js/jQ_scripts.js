$(function() {
    $( "#sortable" ).sortable({
        placeholder: "ui-state-highlight"
    });
    $( "#sortable" ).disableSelection();
});
$(document).ready(function(){
    
    var icon_pencil = '<span class="icon-container floatr ui-corner-all"><span class="ui-icon ui-icon-pencil"></span></span>';     
    var icon_plus =   '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-plusthick"></span></span>';     
    var icon_accept = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-check"></span></span>';     
    var icon_cancel = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-cancel"></span></span>';     
    var icon_delete = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-close"></span></span>';     
    
    
    $( "#ul_vystupy, #ul_aktivity" ).sortable({
        placeholder: "ui-state-highlight"
    });
    $( "#ul_vystupy, #ul_aktivity" ).disableSelection();
    
    
    /* Ikony + na vsechny pole*/
    $("table.matrix h2")
        .addClass("ui-widget-header ui-corner-all")
        .not(".empty_cell")
        .append(icon_plus)
        .hover(
            function(){$(this).find(".icon-container").show(200)},
            function(){$(this).find(".icon-container").hide(200)}
        )
    ;
    /* Zobrazovani ikonek po najeti na li */
    $(document).on({
       mouseenter: function(){
           $(this).find(".icon-container").show(200)}, 
       mouseleave: function(){
           $(this).find(".icon-container").hide(200)} 
    },"li.ui-state-default");
    
    
    /* Odkazy horni nabidky a radky matice - hover effekt*/
    $(document).on({
        mouseenter: function(){$(this).addClass('ui-state-hover').addClass('color_white')},
        mouseleave: function(){$(this).removeClass('ui-state-hover').removeClass('color_white')}
    },"table.matrix ul li,#top ul li");
    
    
    
    /* Zamer a cilu - vzdy jen jeden*/
    $("#zamer h2, #cil h2").each(function(){
        if ($(this).parent().find("li").length == 1){
            // nahradit + za edit
            $(this).find(".icon-container").remove();
            $(this).append(icon_pencil)
            .parent().find(".ui-icon-pencil").click(function(){
                var li = $(this).parents("td").find("li");
//                var oldText = li.text();
                li.data("oldVal",li.html());
                var newLi= '<input id="editing_input" type="text" value="'+li.text()+'"/>'+icon_accept + icon_cancel;
                li.html(newLi);

            });
        }
    });
    
    /* Ukazatele */
    
    
    
    /*
     * Vystupy
     */
    /* Hover vsech aktivit souvisejicich s vystupem */
    $(document).on({
        mouseenter: function(){
           var id = $(this).attr("id");
            $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id).addClass('ui-state-hover'); 
        },
        mouseleave: function(){
            var id = $(this).attr("id");
            $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id).removeClass('ui-state-hover');
        }
    },"table.matrix ul#ul_vystupy li");
    /* Zobrazeni/schovani aktivit vystup po zatrzeni checkboxu */
    $(document).on({
        change: function(){
            $(this).parents("ul").find("input:checkbox").each(function(){
                var id = $(this).parent().attr("id");
                if ($(this).attr("checked")){
                    $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id).show();    
                } else {
                    $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id).hide();    
                }
            });
        }
    },"table.matrix ul#ul_vystupy li input:checkbox");
    
    /* Pridani ikonek na vystupy */
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
                $(this).parents("li").html('<input id="editing_input" type="text" value="'+text+'"/>'+icon_accept + icon_cancel);
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

                $("#inserting_input").focus();
        });
        
        // pri kliknuti na accpet obsluha:
       $(document).on("click",".ui-icon-check",function(){
//           alert("klik");
           var li = $(this).parents("li");
           var input = li.find("input");
           var ul = $(this).parents("ul");
           var action;
           var data = {};
           
           if (input.attr("id") == "inserting_input"){

               // pridani neceho noveho
               switch(ul.attr("id")){
                    case "ul_zamer":
                        action = 'newZamer';
                        data["text"]= input.attr("value");
                        break;
                    case "ul_cil":
                        action = 'edit_cil';     
                        data["text"]= input.attr("value");
                        break;                        
                    case "ul_zamer_uk":
                    case "ul_cil_uk":
                    case "ul_vystupy_uk":
                        // data: text, id radku
                        action = 'new_uk';     
                        data["text"]= input.attr("value");
                        data["row_id"]= ul.parents("tr").attr("id").toString().substr(3);
                        break;                        
                    case "ul_zamer_zdroje":
                    case "ul_cil_zdroje":
                    case "ul_vystupy_zdroje":
                        // data: text, id radku
                        action = 'new_zdroje';     
                        data["text"]= input.attr("value");
                        data["row_id"]= ul.parents("tr").attr("id").toString().substr(3);
                        break;                        

                    case "ul_cil_predpoklady":
                    case "ul_vystupy_predpoklady":
                    case "ul_aktivity_predpoklady":
                    case "ul_pred_podm":
                        // data: text, id radku
                        action = 'new_predpoklady';     
                        data["text"]= input.attr("value");
                        data["row_id"]= ul.parents("tr").attr("id").toString().substr(3);
                        break;                        
                }   
           } else if (input.attr("id") == "editing_input") {
               // editace stare hodnoty

               // najdu, co edituju
               switch(ul.attr("id")){
                    case "ul_zamer":
                        action = 'edit_zamer';
                        data["text"]= input.attr("value");
                        break;
                    case "ul_cil":
                        action = 'edit_cil';     
                        data["text"]= input.attr("value");
                        break;                        
                    case "ul_zamer_uk":
                    case "ul_cil_uk":
                    case "ul_vystupy_uk":
                        // data: text, id ukazatele
                        action = 'edit_uk';     
                        data["text"]= input.attr("value");
                        data["uk_id"]= li.attr("id").toString().substr(2);
                        break;                        
                    case "ul_zamer_zdroje":
                    case "ul_cil_zdroje":
                    case "ul_vystupy_zdroje":
                        // data: text, id radku
                        action = 'edit_zdroje';     
                        data["text"]= input.attr("value");
                        data["zdr_id"]= li.attr("id").toString().substr(3);
                        break;                        
                    
                    case "ul_cil_predpoklady":
                    case "ul_vystupy_predpoklady":
                    case "ul_aktivity_predpoklady":
                    case "ul_pred_podm":
                        // data: text, id radku
                        action = 'edit_predpoklady';     
                        data["text"]= input.attr("value");
                        data["pr_id"]= li.attr("id").toString().substr(2);
                        break;                        
               }                     
           }
           //alert("?do="+action+" ; data: "+data);
           var params = '';
           for (var nazev in data)
               params += '&' + nazev + '=' + data[nazev];
//           alert('?do=' + action + params);
           $.get('?do=' + action + params);
       }); 
       
       $(document).on("click",".ui-icon-cancel",function(){
           
            var li = $(this).parents("li");
            var input = li.find("input");
//            var ul = $(this).parents("ul");
           
            if (input.attr("id") == "inserting_input"){
                li.remove();
            } else if (input.attr("id") == "editing_input") {
                // vratim puvodni hodnotu
                li.html(li.data("oldVal"));
            }
        });
        
        $(document).on("click","li .ui-icon-pencil",function(){
            //editace li - ikonka primo v li
            var li = $(this).parents("li");
            li.data("oldVal",li.html());
            var newLi= '<input id="editing_input" type="text" value="'+li.text()+'"/>'+icon_accept + icon_cancel;
            li.html(newLi);
        });
});