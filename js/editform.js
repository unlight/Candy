jQuery(function() {

	var LastTimeStamp = 0;	
	var SlugHandler = function() {
		var Enabled = true;
		//var Enabled = $('#Form_CreateSection')[0].checked;
		if (Enabled) {
			var Text = $('#Form_Title').val();
			$.post(gdn.url('candy/slug'), {Text:Text}, function(text){
				$("#Form_URI").val(text);
			});
		}
		return false;
	}
	
	// Cookie in Morf plugin
	var LocalCoookie = function(Name, Value) {
		if (typeof Cookie == 'function') return Cookie(Name, Value);
	};

	
	if ($.fn.textpandable) {
		$("textarea.CodeBox").textpandable({speed:0, maxRows:35});
	}
	
	$("#GetSlugButton").live('click', SlugHandler);
	
	$('.ToggleButton').live('click', function(){
		var bRemoveSelf = $(this).hasClass('RemoveSelf');
		var ClassName = $.trim($(this).attr('class').replace('ToggleButton', '')).split(' ')[0];
		var Items = $(this).parents('ul').find('.'+ClassName).not(this);
		var IsVisible = Items.is(':visible');
		LocalCoookie(ClassName, IsVisible ? 1 : 0);
		if (bRemoveSelf) $(this).fadeOut('fast');
		Items['fade' + ((IsVisible) ? 'Out' : 'In')]('fast');
		return false;
	});
	
	$('.TabToggleButton').live('click', function(){
		var ClassName = $.trim($(this).attr('class').replace('TabToggleButton', '')).split(' ')[0];
		$(this).siblings().removeClass('Active');
		$(this).addClass('Active');
		LocalCoookie('CurrentTabToggleButton', ClassName);
		var TextArea = $(this).parent().nextAll('.'+ClassName);
		$(this).parent().nextAll("textarea").not(TextArea).hide();
		TextArea.fadeIn('fast');
		return false;
	});

	// Load saved current TabToggleButton
	var ClassName = LocalCoookie('CurrentTabToggleButton');
	if (ClassName) $('a.TabToggleButton.'+ClassName).click();
	
	// Load saved current ToggleButton
	$('.ToggleButton').each(function(Index, Button){
		var ClassName = $.trim($(this).attr('class').replace('ToggleButton', '')).split(' ')[0];
		var Toggled = LocalCoookie(ClassName);
		if (Toggled) $(this).click();
	});
	
	
	
});