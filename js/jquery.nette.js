/**
 * AJAX Nette Framwork plugin for jQuery
 *
 * @copyright   Copyright (c) 2009 Jan Marek
 * @license     MIT
 * @link        http://nettephp.com/cs/extras/jquery-ajax
 * @version     0.2
 */

jQuery.extend({
	nette: {
		updateSnippet: function (id, html) {
			$("#" + id).html(html);
                        if (refresh_sortable)
                            refresh_sortable();
		},
                
                showMessage : function (msg,status){
                    $("#ajax_msg").text(msg);
                    if (status=='ok')
                        $("#ajax_msg").removeClass("error").addClass("ok");
                    else
                        $("#ajax_msg").removeClass("ok").addClass("error");
                    $("#ajax_msg").slideDown(600,function(){setTimeout('$("#ajax_msg").slideUp(600);',2000);})
                    
                },

		success: function (payload) {
			// redirect
			if (payload.redirect) {
				window.location.href = payload.redirect;
				return;
			}

			// snippets
			if (payload.snippets) {
				for (var i in payload.snippets) {
					jQuery.nette.updateSnippet(i, payload.snippets[i]);
				}
			}
                        if (payload.message && payload.status){
                            jQuery.nette.showMessage(payload.message,payload.status);
                        }
		}
	}
});

jQuery.ajaxSetup({
	success: jQuery.nette.success,
	dataType: "json"
});