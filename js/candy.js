jQuery(function() {
	
	var LibraryRoot = gdn.combinePaths(gdn.definition('WebRoot'), 'js/library');
	
	if (typeof($.fn.livequery) != 'function') $.getScript(gdn.combinePaths(LibraryRoot, 'jquery.livequery.js'));
	if (typeof($.fn.popup) != 'function') {
		$.getScript(gdn.combinePaths(LibraryRoot, 'jquery.popup.js'), function(){
			$('a.Popup').popup();
			$('a.PopConfirm').popup({'confirm': true, 'followConfirm': true});
		});		
	}
	if (typeof($.fn.ajaxForm) != 'function') {
		$.getScript(gdn.combinePaths(LibraryRoot, 'jquery.form.js'));
	}
	if (typeof($.fn.handleAjaxForm) != 'function') {
		$.getScript(gdn.combinePaths(LibraryRoot, 'jquery.gardenhandleajaxform.js'), function(){
			$('.AjaxForm').handleAjaxForm();
		});
	}
	
	var BoolButtonClick = function(){
		var self = this;
		var action = this.href;
		$(self).after('<span class="TinyProgress">&#160;</span>');
		$.ajax({
			type: "POST",
			url: action,
			data: {DeliveryType:'BOOL', DeliveryMethod:'JSON'},
			dataType: 'json',
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				$('.Popup, .Overlay').remove(); // Remove any old popups
				$.popup.settings.sender = self;
				var Message = jQuery.PopupErrorMessage(XMLHttpRequest, textStatus);
				$.popup({}, Message);
			},
			success: function(json) {
				$("a.BoolButton").unbind("click");
				if (json.RedirectUrl) setTimeout("document.location='" + gdn.url(json.RedirectUrl) + "';", 1);
				if (json.Targets) gdn.processTargets(json.Targets);
				return false;
			},
			complete: function(XMLHttpRequest, textStatus) {
				$(self).next().remove();
			}
		});
		return false;
	}
	
	$("a.BoolButton").live("click", BoolButtonClick);

});

jQuery.PopupErrorMessage = function(XMLHttpRequest, textStatus) {
	var Message = '';
	var json = jQuery.parseJSON(XMLHttpRequest.responseText);
	var ErrorText = json.Exception;
	if (!ErrorText) ErrorText = XMLHttpRequest.responseText;
	Message = '<h1>Error</h1><div class="Wrap AjaxError">' + ErrorText + '</div>';
	return Message;
}


/*
* doWhen jQuery plugin
* Copyright 2011, Emmett Pickerel
* Released under the MIT Licence.
*/
!function(a){var b,c,d;b={interval:100},c=function(a){a.test()&&(clearInterval(a.iid),a.cb.call(a.context||window,a.data))},d=function(a){a.iid=setInterval(function(){c(a)},a.interval)},a.doWhen=function(c,e,f){d(a.extend({test:c,cb:e},b,f))}}(window.jQuery);
	
	
