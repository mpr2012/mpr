// TODO: pridani/editace aktivit
// TODO: zobrazeni napovedy
// TODO: zotaveni z chyby pri ukladani do db z ajax pozadavku
// TODO: behem zpracovani ajax pozadavku, nejaky waiting window
// TODO: flash zprava po dokonceni pozadavku

$(document).ready(function(){
    
    var icon_pencil = '<span class="icon-container floatr ui-corner-all"><span class="ui-icon ui-icon-pencil"></span></span>';     
    var icon_plus =   '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-plusthick"></span></span>';     
    var icon_accept = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-check"></span></span>';     
    var icon_cancel = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-cancel"></span></span>';     
    var icon_delete = '<span class="icon-container floatr ui-corner-all"> <span class="ui-icon ui-icon-close"></span></span>';     
    var icon_help =   '<span class="icon-container floatr ui-corner-all"><span class="ui-icon ui-icon-help"></span></span>';     
    
    refresh_sortable();
    /* Ikony + na vsechny pole*/
    $("table.matrix h2")
        .addClass("ui-widget-header ui-corner-all")
        .not(".empty_cell")
        .append(icon_help)
        .hover(
            function(){$(this).find(".icon-container").show(200)},
            function(){$(this).find(".icon-container").hide(200)}
        )
        .not($("table.matrix td").has("#ul_aktivity_zdroje,#ul_aktivity_cas").find("h2"))
        .append(icon_plus)
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
            $(this).find(".icon-container").has(".ui-icon-plusthick").remove();
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
    
    /* Napoveda */
    $(document).on("click", ".ui-icon-help",function(){
        // zjistim, ceho napovedu
        var ul = $(this).parents("td").find("ul");
        var nadpis = $(this).parents("td").find("h2").text();
        var text = "";
        switch(ul.attr("id")){
            case "ul_zamer":
                text = "Bla bla, toto je zamer";
                break;
            case "ul_cil":
                break;                        
            case "ul_zamer_uk":
            case "ul_cil_uk":
            case "ul_vystupy_uk":
                // data: text, id radku
                break;                        
            case "ul_zamer_zdroje":
            case "ul_cil_zdroje":
            case "ul_vystupy_zdroje":
                // data: text, id radku
                break;                        

            case "ul_cil_predpoklady":
            case "ul_vystupy_predpoklady":
            case "ul_aktivity_predpoklady":
            case "ul_pred_podm":
                // data: text, id radku
                break;
            case  "ul_vystupy":
                break;

        } 
        // zobrazim dialog
        if (text){
            $("#dialog")
                .text(text)
                .dialog({
                    title: nadpis,
                    modal:true
                    
                })
            ;
        }
    });
    
    
    
    /*
     * Vystupy
     */
    
    /* Hover vsech aktivit souvisejicich s vystupem */
    $(document).on({
        mouseenter: function(){
           var id = $(this).attr("id");
            $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id)
                .addClass('ui-state-hover') 
                .addClass('color_white');
        },
        mouseleave: function(){
            var id = $(this).attr("id");
            $("#ul_aktivity li."+id+",#ul_aktivity_zdroje li."+id+",#ul_aktivity_cas li."+id)
                .removeClass('ui-state-hover')
                .removeClass('color_white');
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
    
    /******************* Aktivity ****************************/
    /* selectable aktivity */
    $(document).on("click", "#akt_frm_vystupy li",function(){
        $("#akt_frm_vystupy li").removeClass("ui-state-hover").removeClass("color_white");
        $(this).addClass("ui-state-hover").addClass("color_white")
    });
    /* aktivity form kalendare */
    addCalendars();
    
    
    /*Pri najeti na aktivitu - hover cely radek */
    $(document).on({
        mouseenter: function(){
            if ($(this).attr("id") && $(this).attr("id").indexOf("akt") != -1){
                // jsem na aktivite
                var id = $(this).attr("id");
                $("#ul_aktivity_zdroje li."+id+", #ul_aktivity_cas li."+id).addClass("ui-state-hover").addClass("color_white");
            } else {
                // jsem na zdroji nebo case
                var st = $(this).attr("class").indexOf("akt");
                var end = $(this).attr("class").indexOf(" ", st);
                var aktId = end == -1 ? $(this).attr("class").substr(st) : $(this).attr("class").substr(st, end-st);
                $("#ul_aktivity li#"+aktId+", #ul_aktivity_zdroje li."+aktId+", #ul_aktivity_cas li."+aktId).addClass("ui-state-hover").addClass("color_white");
            }
        },
        mouseleave: function(){
            if ($(this).attr("id") && $(this).attr("id").indexOf("akt") != -1){
                // jsem na aktivite
                var id = $(this).attr("id");
                $("#ul_aktivity_zdroje li."+id+", #ul_aktivity_cas li."+id).removeClass("ui-state-hover").removeClass("color_white");
            } else {
                // jsem na zdroji nebo case
                var st = $(this).attr("class").indexOf("akt");
                var end = $(this).attr("class").indexOf(" ", st);
                var aktId = end == -1 ? $(this).attr("class").substr(st) : $(this).attr("class").substr(st, end-st);
                $("#ul_aktivity li#"+aktId+", #ul_aktivity_zdroje li."+aktId+", #ul_aktivity_cas li."+aktId).removeClass("ui-state-hover").removeClass("color_white");
            }
        }
    },"#ul_aktivity li, #ul_aktivity_zdroje li, #ul_aktivity_cas li");
    
                    
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
            if ($("#inserting_input").length){
                // jiz je nekde zobrazen edit input
                $("#inserting_input").focus();
                return;
            } else if ($(this).parents("td").find("ul").attr("id") == "ul_aktivity"){
                // pridani aktivity
                
                $("#snippet--aktivita_form")
//                    .data("action",action)
//                    .data("data",data)
//                    .html('') //<div id="snippet--aktivita_form"></div>
//                    .append($("#snippet--aktivita_form"))
//                    .attr("title", "Nová aktivita")
                    .dialog({
                        width: 800,
                        modal: true,
                        title: "Nová aktivita",
                        buttons: {
                            "Uložit": function() {
                                var action = "new_aktivita";
                                var vys_id = $("#akt_frm_vystupy li.ui-state-hover").attr("id");
                                if (!vys_id){
                                    $("#akt_frm_msg").text("Není vybrán výstup").show();//removeClass("ui-helper-hidden");
                                    return;
                                }
                                var data = {};
                                data['nazev'] = $("#akt_frm_nazev").attr("value");
                                data['zdroje'] = $("#akt_frm_zdroje").attr("value");
                                data['cas_od'] = $("#akt_frm_od").attr("value");
                                data['cas_do'] = $("#akt_frm_do").attr("value");
                                data['vys_id'] = vys_id.substr(3);
                                $.get('?do=' + action, data);
                                console.log('?do='+action);
                                console.log(data);
                                $( this ).dialog( "close" );
                            },
                            "Storno": function() {
                                $( this ).dialog( "close" );
                            }
                        }
                    });
                    return;
            }
            // najde, k cemu pridat li
            $(this).parents("td").find("ul")
            .append('<li class="ui-state-default">'+
                '<input type="text" id="inserting_input" class="edit_input"/>'+
                icon_accept+icon_cancel+
                '</li>')
            .find(".icon-container")

            $("#inserting_input").focus();
        });
        
       /* Ikonka accept */
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
                   case  "ul_vystupy":
                        action = 'new_vystupy';
                        data['text'] = input.attr("value");
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
                   case  "ul_vystupy":
                        action = 'edit_vystupy';
                        data['text'] = $("#editing_input").attr("value");
                        data['vys_id'] = li.attr("id").toString().substr(3);
                        break;
                   
               }                     
           }
           //alert("?do="+action+" ; data: "+data);
           var params = '';
           for (var nazev in data)
               params += '&' + nazev + '=' + encodeURIComponent(data[nazev]);
//           alert('?do=' + action + params);
           $.get('?do=' + action + params);
       }); 
       
       
       /* Ikonka zrusit zmeny */
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
        
        /* Ikonka edit */
        $(document).on("click","li .ui-icon-pencil",function(){
            if ($("#editing_input").length){
                // jiz je nekde zobrazen edit input
                $("#editing_input").focus();
                return;
            }
            //editace li - ikonka primo v li
            var li = $(this).parents("li");
            var ul = $(this).parents("ul");
            switch(ul.attr("id")){
                case "ul_aktivity":
                case "ul_aktivity_zdroje":
                case "ul_aktivity_cas":
                    // otevre editaci v popup
                    var akt_id;
                    if(li.attr("id")) {
                        akt_id = li.attr("id").substr(3);
                    }
                    else {
                        var st = li.attr("class").indexOf("akt")+3;
                        var end = li.attr("class").indexOf(" ", st);
                        akt_id = end == -1 ? li.attr("class").substr(st) : li.attr("class").substr(st, end-st);
                        
                    }
                    $.get('?do=edit_aktivity&akt_id='+akt_id, function(payload){
                        // po tom, co je nacten novy obsah do snippetu zobrazi dialog
                        if (payload.snippets) {
                            for (var i in payload.snippets) {
                                jQuery.nette.updateSnippet(i, payload.snippets[i]);
                            }
			}
                        addCalendars();
                        $("#snippet--aktivita_form")
                            .dialog({
                                width: 800,
                                modal: true,
                                title: "Editace aktivity",
                                buttons: {
                                    "Uložit": function() {
                                        var action = "edit_aktivita";
                                        var vys_id = $("#akt_frm_vystupy li.ui-state-hover").attr("id");
                                        if (!vys_id){
                                            $("#akt_frm_msg").text("Není vybrán výstup").show();//removeClass("ui-helper-hidden");
                                            return;
                                        }
                                        var data = {};
                                        data['nazev'] = $("#akt_frm_nazev").attr("value");
                                        data['zdroje'] = $("#akt_frm_zdroje").attr("value");
                                        data['cas_od'] = $("#akt_frm_od").attr("value");
                                        data['cas_do'] = $("#akt_frm_do").attr("value");
                                        data['vys_id'] = vys_id.substr(3);
                                        data['akt_id'] = $("#akt_frm_id").html();
                                        var params = '';
                                        for (var nazev in data)
                                            params += '&' + nazev + '=' + encodeURIComponent(data[nazev]);
                                //           alert('?do=' + action + params);
                                        console.log(data);
                                        $.get('?do=' + action + params);
                                        //                                $.post('?do='+$(this).data("action"));
                                        $( this ).dialog( "close" );
                                    },
                                    "Storno": function() {
                                        $( this ).dialog( "close" );
                                    }
                                }
                            });
                    });
                    return;
                default:
            }
            li.data("oldVal",li.html());
            var label = li.find("label");
            var newLi;
            if (label.length){
                newLi = '<input id="editing_input" type="text" value="'+$.trim(label.text()).substr(2)+'"/>'+icon_accept + icon_cancel;
            } else {
                newLi= '<input id="editing_input" type="text" value="'+$.trim(li.text())+'"/>'+icon_accept + icon_cancel;
            }
            li.html(newLi);
        });
        
        /* Ikonka delete */
        $(document).on("click","li .ui-icon-close",function(){
            //smazani li - ikonka primo v li
            var li = $(this).parents("li");
            var ul = $(this).parents("ul");
            var action;
            var data = {};
            // najdu, co mazu
            switch(ul.attr("id")){
                case "ul_zamer":
                    action = "delete_zamer";
                    break;
                case "ul_cil":
                    action = "delete_cil";
                    break;
                case "ul_zamer_uk":
                case "ul_cil_uk":
                case "ul_vystupy_uk":
                    action = "delete_uk";
                    data['rec_id'] = li.attr("id").toString().substr(2);
                    break;
                case "ul_zamer_zdroje":
                case "ul_cil_zdroje":
                case "ul_vystupy_zdroje":
                    action = "delete_zdroje";
                    data['rec_id'] = li.attr("id").toString().substr(3);
                    break;
                case "ul_cil_predpoklady":
                case "ul_vystupy_predpoklady":
                case "ul_aktivity_predpoklady":
                case "ul_pred_podm":
                    action = "delete_predpoklady";
                    data['rec_id'] = li.attr("id").toString().substr(2);
                    break;
                case "ul_vystupy":
                    action = "delete_vystupy";
                    data['rec_id'] = li.attr("id").toString().substr(3);
                    break;
                case "ul_aktivity":
                    action = "delete_aktivity";
                    data['rec_id'] = li.attr("id").toString().substr(3);
                    break;
            }
            $("#dialog")
            .data("action",action)
            .data("data",data)
            .text("Opravdu chcete smazat tuto položku: "+li.text())
            .attr("title", "Smazat")
            .dialog({
                modal: true,
                width: 300,
                title: "Smazat",
                buttons: {
                    Ano: function() {
                        $.get('?do='+$(this).data("action"),$(this).data("data"));
                        $( this ).dialog( "close" );
                    },
                    Ne: function() {
                        $( this ).dialog( "close" );
                    }
                }
            });
            
//            li.data("oldVal",li.html());
//            var newLi= '<input id="editing_input" type="text" value="'+li.text()+'"/>'+icon_accept + icon_cancel;
//            li.html(newLi);
        });
});

function addCalendars(){
//    $(".date_input").datepicker($.datepicker.regional['cs']);
    $(".date_input").datepicker({ altFormat: "yy-mm-dd" });
}

function refresh_sortable(){
    $( "#ul_vystupy, #ul_aktivity" ).sortable({
        placeholder: "ui-state-highlight",
        stop: function(){
            console.log("stop");
            if ($(this).attr("id") == "ul_vystupy"){
                // vystupy
                // zjistit nove poradi 
                $(this).find("li").each(function(){
                    var poradi = $(this).prevAll("li").length+1;
                    var data = $(this).parent().data("data");
                    if (!data) data = {};
                    data[$(this).attr("id").substr(3)] = poradi;
                    $(this).parent().data("data",data);
                });
                console.log([$(this).data("data")]);
                $.get('?do=change_seq_vystupy&poradi=' + JSON.stringify($(this).data("data")));
//                $.post('?do=change_seq_vystupy',{
//                    all_data:$(this).data("data")
//                    });
            } else {
                // aktivity
                $(this).find("li").each(function(){
                    var st = $(this).attr("class").indexOf("vys");
                    var end = $(this).attr("class").indexOf(" ", st);
                    var vysId = end == -1 ? $(this).attr("class").substr(st) : $(this).attr("class").substr(st, end-st);
                    
                    var poradi = $(this).prevAll("li."+vysId).length+1;
                    var data = $(this).parent().data("data");
                    if (!data) data = {};
                    data[$(this).attr("id").substr(3)] = poradi;
                    $(this).parent().data("data",data);
                });
                console.log([$(this).data("data")]);
                $.get('?do=change_seq_aktivity&poradi=' + JSON.stringify($(this).data("data")));
//                $.post('?do=change_seq_aktivity',{
//                    all_data:$(this).data("data")
//                    });
            }
        }
    //        out: function(){console.log("out")},
    //        deactivate: function(){console.log("deactivate")}
        
    });
    $( "#ul_vystupy, #ul_aktivity" ).disableSelection();
}
